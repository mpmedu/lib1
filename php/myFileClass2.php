<?php

define('LEN_MAIN_SECTOR',32768);

/* 
$mainsec = array(
  'version' => array(1 => 1, 2 => 1),      // 0,  2
  'fileid' => 'mpm',                       // 2,  3
  'idsIndex' => 0,                         // 5,  2
  'idsUsed' => 0,                          // 7,  2
  'idsEmpty' => 0,                         // 9,  2
  empty byte                               // 11,  1
  'categories' = array( )                  // 12,  1400 * 22 = 30800
    id=1,pn=2,ps=2,nLen=1,nOff=4,lenB=2,offB=4,lenC=2,offC=4   Total = 22
)
 */
 
//***** For pack and unpack the following apply
// C is 8 bit unsigned character
// A is space added string, A3 means get 3 bytes
// V is 32 bit unsigned, little Endian, same as VB5's Long except it is unsigned
// v is 16 bit unsigned, little Endian, same as VB5's Integer except it is unsigned

// Note that unpack returns an object, [1] is the first element
 


class myFileClass2 {
  private $docPath;
  private $fname;
  private $cname;
  private $filename;   // the full file path
  private $version;
  private $base;
  
  private $idsIndex;
  private $idsUsed;
  private $idsEmpty;
  
  private $catarray;
  
  private $highlight;
  
  function __construct($fname,$baseDir=NULL,$cname=NULL,$v1=NULL,$v2=NULL) {
    $this->fname = trim($fname);
    $this->setListName($cname);
    if ($cname != NULL) {$this->cname = $cname;}
    $this->setVersion($v1,$v2);
    if ($baseDir === NULL) {
      $baseDir = __DIR__ . '/../';
    } else {
      $baseDir = trim($baseDir) . '/';
    }
    $this->docPath = $baseDir . DOCPATH;
    $this->filename = $this->docPath . '/' . $this->fname . '.mpm';
  }
  
  public function setListName($cname) {
    if ($cname != NULL) {$this->cname = $cname;} else {$this->cname = $this->fname;}
  }
  
  public function setVersion($v1=NULL,$v2=NULL) {
    if ($v1 != NULL) {$this->version[1] = $v1;} else {$this->version[1] = 1;}
    if ($v2 != NULL) {$this->version[2] = $v2;} else {$this->version[2] = 1;}
  }
  
  public function createNew() {
    if (!is_dir($this->docPath)) {
      mkdir($this->docPath);
    }
    // the following code creates a new file with size LEN_MAIN_SECTOR filled with 0s
    $fp = fopen($this->filename,'w');
    fseek($fp,(LEN_MAIN_SECTOR - 1));
    fwrite($fp,pack('C',0));
    fclose($fp);
    
    // write the file version in bytes 1 and 2, then write mpm
    $fp = fopen($this->filename,'r+');
    fseek($fp,0);
    fwrite($fp,pack('C2',$this->version[1],$this->version[2]));
    fwrite($fp,'mpm');
    fclose($fp);
/*     
    Initialize the counters idsIndex, idsUsed, idsEmpty to 0... they will be saved
    to file when addCategory() is called
 */    
    $this->idsIndex = 0;
    $this->idsUsed = 0;
    $this->idsEmpty = 0;
    // add the first category using cname as the category name
    $this->addCategory($this->cname,-1);  // this also saves the ids variables
  }
  
  public function init() {
    $this->highlight = -1;
    $this->base = LEN_MAIN_SECTOR;
    if (!file_exists($this->filename)) {
      $this->createNew();
    }
    if (filesize($this->filename) < LEN_MAIN_SECTOR) return false;
    // read the whole file and unpack its contents
    $s = file_get_contents($this->filename);
    $this->version = unpack('C2',substr($s,0,2));
    $mpm = substr($s,2,3);
    if ($mpm != 'mpm') return false;
    
    $ta = unpack('v3',substr($s,5,6));
    $this->idsIndex = $ta[1];
    $this->idsUsed = $ta[2];
    $this->idsEmpty = $ta[3];
    
/*     $ta = unpack('C1',substr($s,11,1));
    $this->nPtrSectors = $ta[1];
    $this->base = LEN_MAIN_SECTOR + (LEN_PTR_SECTOR * $this->nPtrSectors);
 */    
    // read the catarray from the file
    for ($i = 0; $i < $this->idsIndex; $i++) {
      $k = 12 + $i * 22;
      $this->catarray[$i] = unpack('Cid/vpn/vps/CnLen/VnOff/vlenB/VoffB/vlenC/VoffC',substr($s,$k,22));
      $this->catarray[$i]['catname'] = substr($s,$this->catarray[$i]['nOff']+$this->base,$this->catarray[$i]['nLen']);
    }
    return true;
  }
  
  public function addCategory($name,$preCat,$isSub=false) {
    if (!file_exists($this->filename)) return false;
    if ($this->idsUsed >= 1400) return false;
    //$name = htmlspecialchars($name);
    $len = strlen($name);
    $willFit = false;
    // find the next free id number in $nextid
    if ($this->idsUsed === $this->idsIndex) {
      $nextid = $this->idsIndex;
      $this->idsIndex++;
    } else {   // there must be holes in the array, ie there are empty ids, ie categories were removed
      $nextid = -1;
      for ($i = 0; $i < $this->idsIndex; $i++) {
        if ($this->catarray[$i]['id'] === 0) {
          $nextid = $i;
          $this->idsEmpty--;
          // check if enough space for new category name
          if ($this->catarray[$i]['nLen'] >= $len)  $willFit = true;
          break;
        }
      }
      if ($nextid === -1) return false;   // should not happen
    }
    $this->highlight = $nextid;
    $this->idsUsed++;
    $this->saveidsToFile();
    $this->catarray[$nextid]['id'] = 1;   // means it is used
    $this->catarray[$nextid]['pn'] = 0;
    $this->catarray[$nextid]['ps'] = 0;
    $this->catarray[$nextid]['catname'] = $name;
    $this->catarray[$nextid]['nLen'] = $len;
    if (!$willFit) {
      $this->catarray[$nextid]['nOff'] = filesize($this->filename) - $this->base;
    }  // else nOff remains the same
    // save the category name to file
    $fp = fopen($this->filename,'r+');
    fseek($fp,$this->catarray[$nextid]['nOff'] + $this->base);
    fwrite($fp,$name);
    fclose($fp);
    // save the category to file
    // if first category(ie preCat = -1) then no linkage otherwise link from the previous category
    if ($preCat != -1) {
      $s = ($isSub)? 'ps' : 'pn';
      $nextp = $this->catarray[$preCat][$s];
      $this->catarray[$preCat][$s] = $nextid;
      $this->catarray[$nextid]['pn'] = $nextp;
      $this->saveOneCatToFile($preCat);
    }
    $this->saveOneCatToFile($nextid);
    return true;
  }
  
  public function editCategory($name,$catid) {
    if (!file_exists($this->filename)) return false;
    //$name = htmlspecialchars($name);
    $len = strlen($name);
    $willFit = false;
    if ($this->catarray[$catid]['nLen'] >= $len) $willFit = true;
    $this->catarray[$catid]['catname'] = $name;
    $this->catarray[$catid]['nLen'] = $len;
    if (!$willFit) {
      $this->catarray[$catid]['nOff'] = filesize($this->filename) - $this->base;
    }  // else nOff remains the same
    // save the category name to file
    $fp = fopen($this->filename,'r+');
    fseek($fp,$this->catarray[$catid]['nOff'] + $this->base);
    fwrite($fp,$name);
    fclose($fp);
    // save the category to file
    $this->saveOneCatToFile($catid);
    $this->highlight = $catid;
    return true;
  }
  
  public function moveCategory($preCat,$isSub,$id2move,$pre2move,$isSub2move) {
    if (!file_exists($this->filename)) return false;
    // link id2move in its new position
    $s = ($isSub)? 'ps' : 'pn';
    $nextp = $this->catarray[$preCat][$s];
    $nextp2move = $this->catarray[$id2move]['pn'];
    $this->catarray[$preCat][$s] = $id2move;
    $this->catarray[$id2move]['pn'] = $nextp;
    $this->saveOneCatToFile($preCat);
    $this->saveOneCatToFile($id2move);
    // fix up link where id2move came from
    $s = ($isSub2move)? 'ps' : 'pn';
    $this->catarray[$pre2move][$s] = $nextp2move;
    $this->saveOneCatToFile($pre2move);
    $this->highlight = $id2move;
    return true;
  }
    
  public function removeCategory($preCat,$isSub) {
    if (!file_exists($this->filename)) return false;
    if ($preCat === -1) {  // remove everything under the heading
      // first put link from heading to be 0
      $catid = $this->catarray[0]['ps'];  // $catid is the first cat in first line to be removed
      if ($catid > 0) {
        $this->catarray[0]['ps'] = 0;
        $this->saveOneCatToFile(0);
        // now remove $catid and its siblings and then everything else
        if (!$this->removeACat($catid,true)) return false;
      }
      $this->highlight = 0;
    } else {  // remove the cat, not its siblings, but all its subcategories 
      // fix up link where $catid is removed
      $s = ($isSub)? 'ps' : 'pn';
      $catid = $this->catarray[$preCat][$s];  // $catid is the cat to be removed
      $this->catarray[$preCat][$s] = $this->catarray[$catid]['pn'];
      $this->saveOneCatToFile($preCat);
      // call removeACat() which will remove the category and all its sub categories
      if (!$this->removeACat($catid,false)) return false; // don't remove siblings
      $this->highlight = $preCat;
    }
    return true;
  }
  
/*   
  If $doSiblings is true then the categories on the same level will be removed, ie pn categories
  If $doSiblings is false then the subcategory will be removed, ie ps category
 */  
  private function removeACat($catid,$doSiblings=true) {
    if ($doSiblings) {
      $pn = $this->catarray[$catid]['pn'];
      if ($pn > 0) $this->removeACat($pn);
    }
    $ps = $this->catarray[$catid]['ps'];
    if ($ps > 0) $this->removeACat($ps);
    // only now can you change the $catid values
    //if ($catid > 0) {  // should not happen, code in js prohibits this because the heading can't be removed
      $this->catarray[$catid]['id'] = 0;   // means it is empty
      $this->catarray[$catid]['pn'] = 0;
      $this->catarray[$catid]['ps'] = 0;
      $this->saveOneCatToFile($catid);
      $this->idsUsed--;
      $this->idsEmpty++;
      if (!$this->saveidsToFile()) return false;
    //}
    return true;
  }
  
  private function saveidsToFile() {
    if (($this->idsUsed + $this->idsEmpty) != $this->idsIndex) return false;
    $fp = fopen($this->filename,'r+');
    $s = pack('v3',$this->idsIndex,$this->idsUsed,$this->idsEmpty);
    fseek($fp,5);
    fwrite($fp,$s);
    fclose($fp);
    return true;
  }
  
  private function saveOneCatToFile($i) {
    $fp = fopen($this->filename,'r+');
    $s = pack('C',$this->catarray[$i]['id']);
    $s .= pack('v2',$this->catarray[$i]['pn'],$this->catarray[$i]['ps']);
    $s .= pack('CV',$this->catarray[$i]['nLen'],$this->catarray[$i]['nOff']);
    fseek($fp,12 + $i * 22);
    fwrite($fp,$s);
    fclose($fp);
  }
  
/*   
  //I am saving one cat at a time so I am not using this sub for now
  private function saveCatarray() {
    $fp = fopen($this->filename,'r+');
    for ($i = 0; $i < $this->idsIndex; $i++) {
      $s = pack('C3',$this->catarray[$i]['id'],$this->catarray[$i]['pn'],$this->catarray[$i]['ps']);
      $s .= pack('CV',$this->catarray[$i]['nLen'],$this->catarray[$i]['nOff']);
      fseek($fp,9 + $i * 12);
      fwrite($fp,$s);
    }
    fclose($fp);
  }
 */
 
  public function makemarrays() {
    for ($i = 0; $i < $this->idsIndex; $i++) {
      $ma_cn[] = '"' . $this->catarray[$i]['catname'] . '"';
      $ma_pn[] = $this->catarray[$i]['pn'];
      $ma_ps[] = $this->catarray[$i]['ps'];
    }
    // make the parr[] array... which calls the recursive function parr_fixsub()
    $this->initArray($parr);
    //$parr[0] = 0;   // not needed now after initArray()
    if ($this->parr_fixsub($parr,0) === false) return false;
    //ksort($parr);   // not needed now after initArray()
    // make the barr[] array
    $this->initArray($barr);
    //$barr[0] = 0;   // not needed now after initArray()
    for ($i = 0; $i < $this->idsIndex; $i++) {
      $p = $this->catarray[$i]['ps'];
      if ($p > 0) {
        if ($barr[$p] != 0) return false;
        $barr[$p] = -$i;
      }
      $p = $this->catarray[$i]['pn'];
      if ($p > 0) {
        if ($barr[$p] != 0) return false;
        $barr[$p] = $i;
      }
    }
    //ksort($barr);   // not needed now after initArray()
    
    $s[] = '"ma_cn":[' . implode($ma_cn,',') . "],";
    $s[] = '"ma_pn":[' . implode($ma_pn,',') . "],";
    $s[] = '"ma_ps":[' . implode($ma_ps,',') . "],";
    $s[] = '"ma_used":' . $this->idsUsed . ",";
    $s[] = '"ma_highlight":' . $this->highlight . ",";
    $s[] = '"ma_parr":[' . implode($parr,',') . "],";
    $s[] = '"ma_barr":[' . implode($barr,',') . "]";
    
    return $s;
  }
  
  private function parr_fixsub(&$parr,$p) {
    $pn = $this->catarray[$p]['ps'];
    while ($pn != 0) {
      if ($parr[$pn] != 0) return false;
      $parr[$pn] = $p;
      if ($this->catarray[$pn]['ps'] > 0) {
        if ($this->parr_fixsub($parr, $pn) === false) return false;
      }
      $pn = $this->catarray[$pn]['pn'];
    }
    return true;
  }
  
  private function initArray(&$arr) {
    for ($i = 0; $i < $this->idsIndex; $i++) {
      $arr[$i] = 0;
    }
  }

}
?>

