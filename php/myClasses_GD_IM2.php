<?php

  define('MAX_W', 1100);  
  define('MAX_H', 600);  

// ****************************************************************
// ******************************** GD class ******************
// ****************************************************************
class GDImage {
  private $fname;
  private $thumb;
  private $width;
  private $height;
  private $type;
  private $orgimage;
  private $newimage;
  
  private $new;
  private $wnew;
  private $hnew;
    
	public static $QUALITY_LOW = 1;
	public static $QUALITY_MEDIUM = 2;
	public static $QUALITY_HIGH = 3;
    
  public function __construct($fName) {
    $this->fname = $fName;
		if(!$information = getimagesize( $this->fname ) ) throw new Exception('Error getting image info');
    $this->width=$information[0]; 
    $this->height=$information[1];
    $this->type=$information[2];
    $this->createImage($this->fname, $this->type, $this->orgimage);
  }
  
  public function checkImageSize($w, $h) {
    if ($this->width < $w) return;
    if ($this->height < $h) return;
    return 1;
  }
  
  public function createThumbnail($w, $h) {
    if ($this->width <> $this->height) {
      if ($this->width > $this->height) {
        $px = round(($this->width- $this->height) / 2);
        $py = 0;
        $d = $this->height;
      } else {
        $py = round(($this->height- $this->width) / 2);
        $px = 0;
        $d = $this->width;
      }
      $this->crop_resize($this->thumb, $this->orgimage, $this->type,  $w,$h, $d, $d, $px, $py);
    } else {
      if ($this->width == $w) {
        $this->thumb = $this->orgimage;
      } else {
        //$this->thumb = imagescale($orgimage, $w,$h);  // only in php 5.5
        $this->crop_resize($this->thumb, $this->orgimage, $this->type, $w, $h, $this->width, $this->width, 0, 0);
      }
    }  
    return 1;
  }
  
  public function createNew($w, $h) {
    if ($w < 0 || $h < 0) {
      if ($w < 0 && $h < 0) {
        $w = -$w;  $h = -$h;
        $r1 = $w / $h;
        $r2 = $this->width / $this->height;
        if ($r1 > $r2) {
          $this->hnew = $h;
          $this->wnew = round($r2 * $h);
          $this->wnew = round($r2 * $h);
        } else {
          $this->wnew = $w;
          $this->hnew = round($w / $r2);
          $this->hnew = round($w / $r2);
        }
      } else if ($w < 0) {
        $this->hnew = $h;
        $this->wnew = round(($this->width / $this->height) * $h);
        
      } else {  // ($h < 0)
        $this->wnew = $w;
        $this->hnew = round(($this->height/ $this->width) * $w);
        
      }
      $this->crop_resize($this->new, $this->orgimage, $this->type, $this->wnew, $this->hnew, $this->width, $this->height, 0, 0);
    } else {
      if ($w === 0) $w = $this->width;
      if ($h === 0) $h = $this->height;
      $this->wnew = $w;
      $this->hnew = $h;
      $w2 = round(($this->height / $h) * $w);
      if ($this->height < $h) {
        $this->hnew = $this->height;
        if ($this->width > $w) {
          $px = round(($this->width - $w) / 2);
          $py = 0;
          $this->crop_resize($this->new, $this->orgimage, $this->type,  $w,$this->hnew, $w, $this->height, $px, $py);
        } else {
          $this->new = $this->orgimage;
          $this->wnew = $this->width;
        }
      } else if ($this->width > $w2) {
        $px = round(($this->width - $w2) / 2);
        $py = 0;
        $this->crop_resize($this->new, $this->orgimage, $this->type,  $w,$h, $w2, $this->height, $px, $py);
      } else if ($this->height == $h) { 
        $this->new = $this->orgimage;
      } else {
        $this->wnew = round(($this->width / $this->height) * $h);
        $this->crop_resize($this->new, $this->orgimage, $this->type, $this->wnew, $h, $this->width, $this->height, 0, 0);
      }
    }      
    return 1;
  }
  
  
	private function createImage($_imageFileName, $_imagetype, &$_image) {
		switch( $_imagetype ) {
			case IMAGETYPE_JPEG:
				$_image = imagecreatefromjpeg( $_imageFileName );
			  break;
			case IMAGETYPE_GIF:
				$_image = imagecreatefromgif( $_imageFileName );
			  break;
			case IMAGETYPE_PNG:
				$_image = imagecreatefrompng( $_imageFileName );
			  break;
			default:
				throw new Exception( "The image is not a valid type" );
			break;
		}
	}
  
	private function crop_resize( &$img, $_image, $_imagetype, $newW, $newH, $width, $height, $sX = 0, $sY = 0 ) {
  	$img = imagecreatetruecolor( $newW, $newH );
		if( $_imagetype == IMAGETYPE_PNG || $_imagetype == IMAGETYPE_GIF ) {
			imagealphablending( $img, false );
			imagesavealpha( $img, true );
		}
		imagecopyresampled( $img, $_image, 0, 0, $sX, $sY, $newW, $newH, $width, $height );
	}
  
	private function saveImage($img,  $filepath, $valueQuality = 3 ) {
		if( file_exists( $filepath ) ) {
      unlink($filepath);
    }
		$quality = $this->getQuality( $valueQuality );
		//@chmod( $directory, 755 );
		switch( $this->type ) {
			case IMAGETYPE_JPEG:
        imagejpeg( $img, $filepath, $quality );
        break;
			case IMAGETYPE_GIF:
					imagegif( $img, $filepath, $quality );
			break;
			case IMAGETYPE_PNG:
					imagepng( $img, $filepath, $quality );
			break;
			default:
				throw new Exception( "The image is not a recognized type" );
        return;
			break;
		}
    return 1;
	}
  
	private function getQuality( $valueQuality ){
		if( $this->type == IMAGETYPE_JPEG ){
			if( $valueQuality == self::$QUALITY_LOW ){
				return 25;
			} elseif( $valueQuality == self::$QUALITY_MEDIUM ){
				return 50;
			} else {
				return 75;
			}
		} elseif( $this->type == IMAGETYPE_GIF || $this->type == IMAGETYPE_PNG ){
			if( $valueQuality == self::$QUALITY_LOW ){
				return 9;
			} elseif( $valueQuality == self::$QUALITY_MEDIUM ){
				return 6;
			} else {
				return 3;
			}
		}
	}
  
  private function trimImage($maxW, $maxH, &$photo) {
    try {
      if ($this->width < $maxW) {
        if ($this->height < $maxH) {
          $photo = sizephoto($this->width, $this->height, $photo);
          return;   // no change made to the original image so return false
        } else {
           // reduce image height
           $h = $maxH;
           $w = round($this->width * $h / $this->height);
        }
      } else {
        if ($this->height < $maxH) {
          // reduce image width
           $w = $maxW;
           $h = round($this->height * $w / $this->width);
        } else {
          // check which controls
          if ($this->width/$this->height > $maxW/$maxH) {
            // reduce image width
           $w = $maxW;
           $h = round($this->height * $w / $this->width);
          } else {
            // reduce image height
           $h = $maxH;
           $w = round($this->width * $h / $this->height);
          }
        }  
      }
      $this->crop_resize($this->newimage, $this->orgimage, $this->type,  $w, $h, $this->width, $this->height, 0, 0);
      $photo = sizephoto($w, $h, $photo);
      return 1;
    } catch (Exception $e) {
      return;
    }
    //return 1;
  }  
 
  public function savefiles($f1, &$f2, $path, $tname) {
    if (!$this->saveImage($this->thumb, $path . $f1)) return;
    if ($this->trimImage(MAX_W,MAX_H,$f2)) {
      if (!$this->saveImage($this->newimage, $path . $f2)) return;
    } else {
      if (!move_uploaded_file($tname, $path . $f2)) return;
    }
    return 1;
  }

  public function saveNew(&$f1, $path) {
    $f1 = sizephoto($this->wnew, $this->hnew, $f1);
    if (!$this->saveImage($this->new, $path . $f1)) return;
    return 1;
  }
  
} 

// ****************************************************************
// ******************************** IMagic class ******************
// ****************************************************************
class IMImage {
  private $fname;
  private $thumb;
  private $width;
  private $height;
  private $newImage;
  
  private $new;
  private $wnew;
  private $hnew;
    
  public function __construct($fName) {
    $this->fname = $fName;
		if(!$information = getimagesize( $this->fname ) ) throw new Exception('Error getting image info');
    $this->thumb=new Imagick($this->fname);
    $geo=$this->thumb->getImageGeometry(); 
    $this->width=$geo['width']; 
    $this->height=$geo['height']; 
  }
  
  public function checkImageSize($w, $h) {
    if ($this->width < $w) return;
    if ($this->height < $h) return;
    return 1;
  }
  
  public function createThumbnail($w, $h) {
    try {
      $wh = $this->width;
      if ($this->width <> $this->height) {
        if ($this->width > $this->height) {
          $px = round(($this->width - $this->height) / 2);
          //$px = ($this->width - $this->height) / 2;
          $py = 0;
          $wh = $this->height;
        } else {
          $py = round(($this->height - $this->width) / 2);
          //$py = ($this->height - $this->width) / 2;
          $px = 0;
        }
        $this->thumb->cropImage($wh, $wh, $px, $py);
      }
      if ($w == $wh) return 1;
      $this->thumb->scaleImage($w, $h); 
      $this->thumb->setImagePage(0, 0, 0, 0);
      return 1;
    } catch (Exception $e) {
      return;
    }
  }  
  
  // public function createLogo($w, $h) {
  // // this is a trial to check if height < $h
  // // this still needs to be tested for when $this->height <= $h
    // $this->logo=new Imagick($this->fname);
    // $this->wlogo = $w;
    // $this->hlogo = $h;
    // if ($this->height <= $h) {
      // $this->hlogo = $this->height;
      // if ($this->width <= $w) {$this->wlogo = $this->width; return 1;}
      // $w2 = $w;
    // } else {
      // $w2 = round(($this->height / $h) * $w);
    // }
    // if ($this->width > $w2) {
      // $px = round(($this->width - $w2) / 2);
      // $py = 0;
      // $this->logo->cropImage($w2, $this->height, $px, $py);
    // } else $w2 = $this->width;
    // if ($this->height <= $h) { 
      // $this->wlogo = $w2;
    // } else {
      // $this->wlogo = round(($w2 / $this->height) * $h);
      // $this->logo->scaleImage($this->wlogo, $h); 
      // $this->logo->setImagePage(0, 0, 0, 0);
    // }  
    // return 1;
  // }
  
  // public function createTemp($w, $h) {
  // // this is a trial to check if height < $h
  // // this still needs to be tested for when $this->height <= $h
    // $this->temp=new Imagick($this->fname);
    // $this->wtemp = $w;
    // $this->htemp = $h;
    // if ($this->height <= $h) {
      // $this->htemp = $this->height;
      // if ($this->width <= $w) {$this->wtemp = $this->width; return 1;}
      // $w2 = $w;
    // } else {
      // $w2 = round(($this->height / $h) * $w);
    // }
    // if ($this->width > $w2) {
      // $px = round(($this->width - $w2) / 2);
      // $py = 0;
      // $this->temp->cropImage($w2, $this->height, $px, $py);
    // } else $w2 = $this->width;
    // if ($this->height <= $h) { 
      // $this->wtemp = $w2;
    // } else {
      // $this->wtemp = round(($w2 / $this->height) * $h);
      // $this->temp->scaleImage($this->wtemp, $h); 
      // $this->temp->setImagePage(0, 0, 0, 0);
    // }  
    // return 1;
  // }
  
  public function createNew($w, $h) {
    $this->new=new Imagick($this->fname);
    if ($w < 0 || $h < 0) {
      if ($w < 0 && $h < 0) {
        $w = -$w;  $h = -$h;
        $r1 = $w / $h;
        $r2 = $this->width / $this->height;
        if ($r1 > $r2) {
          $this->hnew = $h;
          $this->wnew = round($r2 * $h);
        } else {
          $this->wnew = $w;
          $this->hnew = round($w / $r2);
        }
      } else if ($w < 0) {
        $this->hnew = $h;
        $this->wnew = round(($this->width / $this->height) * $h);
      } else {  // ($h < 0)
        $this->wnew = $w;
        $this->hnew = round(($this->height/ $this->width) * $w);
      }
      $this->new->scaleImage($this->wnew, $this->hnew); 
      $this->new->setImagePage(0, 0, 0, 0);
    } else {
      if ($w === 0) $w = $this->width;
      if ($h === 0) $h = $this->height;
      
      
      $this->wnew = $w;
      $this->hnew = $h;
      if ($this->height <= $h) {
        $this->hnew = $this->height;
        if ($this->width <= $w) {$this->wnew = $this->width; return 1;}
        $w2 = $w;
      } else {
        $w2 = round(($this->height / $h) * $w);
      }
      if ($this->width > $w2) {
        $px = round(($this->width - $w2) / 2);
        $py = 0;
        $this->new->cropImage($w2, $this->height, $px, $py);
        $this->new->setImagePage(0, 0, 0, 0);
      } else $w2 = $this->width;
      if ($this->height <= $h) { 
        $this->wnew = $w2;
      } else {
        $this->wnew = round(($w2 / $this->height) * $h);
        $this->new->scaleImage($this->wnew, $h); 
        $this->new->setImagePage(0, 0, 0, 0);
      }
    }
    return 1;
  }

  private function trimImage($maxW, $maxH, &$photo) {
    try {
      if ($this->width < $maxW) {
        if ($this->height < $maxH) {
          $w = $this->width;
          $h = $this->height;
          $photo = sizephoto($this->width, $this->height, $photo);
          return;
        } else {
           // reduce image height
           $this->resizeImage(0,$maxH);
        }
      } else {
        if ($this->height < $maxH) {
          // reduce image width
          $this->resizeImage($maxW,0);
        } else {
          // check which controls
          if ($this->width/$this->height > $maxW/$maxH) {
            // reduce image width
            $this->resizeImage($maxW,0);
          } else {
            // reduce image height
            $this->resizeImage(0,$maxH);
          }
        }  
      }
      $geo=$this->newImage->getImageGeometry(); 
      $photo = sizephoto($geo['width'], $geo['height'], $photo);
      return 1;
    } catch (Exception $e) {
      return;
    }
    //return 1;
  }  
  
  private function resizeImage($newW, $newH) {
    $this->newImage=new Imagick($this->fname);
    $this->newImage->scaleImage($newW, $newH); 
    $this->newImage->setImagePage(0, 0, 0, 0);
  }
  
  
	private function saveThumb(  $filepath ) {
    //$this->thumb->writeImage(realpath($filepath));
    $this->thumb->writeImage($filepath);
    $this->thumb->destroy(); 
    return 1;
  }
  
	private function saveNewImage(  $filepath ) {
    //$this->thumb->writeImage(realpath($filepath));
    $this->newImage->writeImage($filepath);
    $this->newImage->destroy(); 
    return 1;
  }
  
	private function saveImage($img,  $filepath ) {
    $img->writeImage($filepath);
    $img->destroy(); 
    return 1;
  }
  
  
public function savefiles($f1, &$f2, $path, $tname) {
  if (!$this->saveThumb($path . $f1)) return;
  if ($this->trimImage(MAX_W,MAX_H, $f2)) {
    if (!$this->saveNewImage($path . $f2)) return;
  } else {
    if (!move_uploaded_file($tname, $path . $f2)) return;
  }
  return 1;
}

  public function saveNew(&$f1, $path) {
    $f1 = sizephoto($this->wnew, $this->hnew, $f1);
    if (!$this->saveImage($this->new, $path . $f1)) return;
    return 1;
  }
  
} 

function sizephoto($w, $h, $fname) {
  $pos = strrpos($fname,'.');
  return substr($fname, 0, $pos) . '_' . $w . "x" . $h . substr($fname, $pos);
}
 
?>
