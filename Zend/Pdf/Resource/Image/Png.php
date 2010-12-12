<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Png.php 23395 2010-11-19 15:30:47Z alexander $
 */

/**
 * @namespace
 */
namespace Zend\Pdf\Resource\Image;
use Zend\Pdf;
use Zend\Pdf\Element;
use Zend\Pdf\Element;
use Zend\Pdf\Element;
use Zend\Pdf\ElementFactory;
use Zend\Pdf\Element;

/** Internally used classes */
require_once 'Zend/Pdf/Element/Array.php';
require_once 'Zend/Pdf/Element/Dictionary.php';
require_once 'Zend/Pdf/Element/Name.php';
require_once 'Zend/Pdf/Element/Numeric.php';
require_once 'Zend/Pdf/Element/String/Binary.php';


/** Zend_Pdf_Resource_Image */
require_once 'Zend/Pdf/Resource/Image.php';

/**
 * PNG image
 *
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Png extends Image
{
    const PNG_COMPRESSION_DEFAULT_STRATEGY = 0;
    const PNG_COMPRESSION_FILTERED = 1;
    const PNG_COMPRESSION_HUFFMAN_ONLY = 2;
    const PNG_COMPRESSION_RLE = 3;

    const PNG_FILTER_NONE = 0;
    const PNG_FILTER_SUB = 1;
    const PNG_FILTER_UP = 2;
    const PNG_FILTER_AVERAGE = 3;
    const PNG_FILTER_PAETH = 4;

    const PNG_INTERLACING_DISABLED = 0;
    const PNG_INTERLACING_ENABLED = 1;

    const PNG_CHANNEL_GRAY = 0;
    const PNG_CHANNEL_RGB = 2;
    const PNG_CHANNEL_INDEXED = 3;
    const PNG_CHANNEL_GRAY_ALPHA = 4;
    const PNG_CHANNEL_RGB_ALPHA = 6;

    protected $_width;
    protected $_height;
    protected $_imageProperties;

    /**
     * Object constructor
     *
     * @param string $imageFileName
     * @throws Zend_Pdf_Exception
     * @todo Add compression conversions to support compression strategys other than PNG_COMPRESSION_DEFAULT_STRATEGY.
     * @todo Add pre-compression filtering.
     * @todo Add interlaced image handling.
     * @todo Add support for 16-bit images. Requires PDF version bump to 1.5 at least.
     * @todo Add processing for all PNG chunks defined in the spec. gAMA etc.
     * @todo Fix tRNS chunk support for Indexed Images to a SMask.
     */
    public function __construct($imageFileName)
    {
        if (($imageFile = @fopen($imageFileName, 'rb')) === false ) {
            require_once 'Zend/Pdf/Exception.php';
            throw new Pdf\Exception( "Can not open '$imageFileName' file for reading." );
        }

        parent::__construct();

        //Check if the file is a PNG
        fseek($imageFile, 1, SEEK_CUR); //First signature byte (%)
        if ('PNG' != fread($imageFile, 3)) {
            require_once 'Zend/Pdf/Exception.php';
            throw new Pdf\Exception('Image is not a PNG');
        }
        fseek($imageFile, 12, SEEK_CUR); //Signature bytes (Includes the IHDR chunk) IHDR processed linerarly because it doesnt contain a variable chunk length
        $wtmp = unpack('Ni',fread($imageFile, 4)); //Unpack a 4-Byte Long
        $width = $wtmp['i'];
        $htmp = unpack('Ni',fread($imageFile, 4));
        $height = $htmp['i'];
        $bits = ord(fread($imageFile, 1)); //Higher than 8 bit depths are only supported in later versions of PDF.
        $color = ord(fread($imageFile, 1));

        $compression = ord(fread($imageFile, 1));
        $prefilter = ord(fread($imageFile,1));

        if (($interlacing = ord(fread($imageFile,1))) != Png::PNG_INTERLACING_DISABLED) {
            require_once 'Zend/Pdf/Exception.php';
            throw new Pdf\Exception( "Only non-interlaced images are currently supported." );
        }

        $this->_width = $width;
        $this->_height = $height;
        $this->_imageProperties = array();
        $this->_imageProperties['bitDepth'] = $bits;
        $this->_imageProperties['pngColorType'] = $color;
        $this->_imageProperties['pngFilterType'] = $prefilter;
        $this->_imageProperties['pngCompressionType'] = $compression;
        $this->_imageProperties['pngInterlacingType'] = $interlacing;

        fseek($imageFile, 4, SEEK_CUR); //4 Byte Ending Sequence
        $imageData = '';

        /*
         * The following loop processes PNG chunks. 4 Byte Longs are packed first give the chunk length
         * followed by the chunk signature, a four byte code. IDAT and IEND are manditory in any PNG.
         */
        while (!feof($imageFile)) {
            $chunkLengthBytes = fread($imageFile, 4);
            if ($chunkLengthBytes === false) {
                require_once 'Zend/Pdf/Exception.php';
                throw new Pdf\Exception('Error ocuured while image file reading.');
            }

            $chunkLengthtmp = unpack('Ni', $chunkLengthBytes);
            $chunkLength    = $chunkLengthtmp['i'];
            $chunkType      = fread($imageFile, 4);
            switch($chunkType) {
                case 'IDAT': //Image Data
                    /*
                     * Reads the actual image data from the PNG file. Since we know at this point that the compression
                     * strategy is the default strategy, we also know that this data is Zip compressed. We will either copy
                     * the data directly to the PDF and provide the correct FlateDecode predictor, or decompress the data
                     * decode the filters and output the data as a raw pixel map.
                     */
                    $imageData .= fread($imageFile, $chunkLength);
                    fseek($imageFile, 4, SEEK_CUR);
                    break;

                case 'PLTE': //Palette
                    $paletteData = fread($imageFile, $chunkLength);
                    fseek($imageFile, 4, SEEK_CUR);
                    break;

                case 'tRNS': //Basic (non-alpha channel) transparency.
                    $trnsData = fread($imageFile, $chunkLength);
                    switch ($color) {
                        case Png::PNG_CHANNEL_GRAY:
                            $baseColor = ord(substr($trnsData, 1, 1));
                            $transparencyData = array(new Element\Numeric($baseColor),
                                                      new Element\Numeric($baseColor));
                            break;

                        case Png::PNG_CHANNEL_RGB:
                            $red = ord(substr($trnsData,1,1));
                            $green = ord(substr($trnsData,3,1));
                            $blue = ord(substr($trnsData,5,1));
                            $transparencyData = array(new Element\Numeric($red),
                                                      new Element\Numeric($red),
                                                      new Element\Numeric($green),
                                                      new Element\Numeric($green),
                                                      new Element\Numeric($blue),
                                                      new Element\Numeric($blue));
                            break;

                        case Png::PNG_CHANNEL_INDEXED:
                            //Find the first transparent color in the index, we will mask that. (This is a bit of a hack. This should be a SMask and mask all entries values).
                            if(($trnsIdx = strpos($trnsData, "\0")) !== false) {
                                $transparencyData = array(new Element\Numeric($trnsIdx),
                                                          new Element\Numeric($trnsIdx));
                            }
                            break;

                        case Png::PNG_CHANNEL_GRAY_ALPHA:
                            // Fall through to the next case

                        case Png::PNG_CHANNEL_RGB_ALPHA:
                            require_once 'Zend/Pdf/Exception.php';
                            throw new Pdf\Exception( "tRNS chunk illegal for Alpha Channel Images" );
                            break;
                    }
                    fseek($imageFile, 4, SEEK_CUR); //4 Byte Ending Sequence
                    break;

                case 'IEND';
                    break 2; //End the loop too

                default:
                    fseek($imageFile, $chunkLength + 4, SEEK_CUR); //Skip the section
                    break;
            }
        }
        fclose($imageFile);

        $compressed = true;
        $imageDataTmp = '';
        $smaskData = '';
        switch ($color) {
            case Png::PNG_CHANNEL_RGB:
                $colorSpace = new Element\Name('DeviceRGB');
                break;

            case Png::PNG_CHANNEL_GRAY:
                $colorSpace = new Element\Name('DeviceGray');
                break;

            case Png::PNG_CHANNEL_INDEXED:
                if(empty($paletteData)) {
                    require_once 'Zend/Pdf/Exception.php';
                    throw new Pdf\Exception( "PNG Corruption: No palette data read for indexed type PNG." );
                }
                $colorSpace = new Element\Array();
                $colorSpace->items[] = new Element\Name('Indexed');
                $colorSpace->items[] = new Element\Name('DeviceRGB');
                $colorSpace->items[] = new Element\Numeric((strlen($paletteData)/3-1));
                $paletteObject = $this->_objectFactory->newObject(new Element\String\Binary($paletteData));
                $colorSpace->items[] = $paletteObject;
                break;

            case Png::PNG_CHANNEL_GRAY_ALPHA:
                /*
                 * To decode PNG's with alpha data we must create two images from one. One image will contain the Gray data
                 * the other will contain the Gray transparency overlay data. The former will become the object data and the latter
                 * will become the Shadow Mask (SMask).
                 */
                if($bits > 8) {
                    require_once 'Zend/Pdf/Exception.php';
                    throw new Pdf\Exception("Alpha PNGs with bit depth > 8 are not yet supported");
                }

                $colorSpace = new Element\Name('DeviceGray');

                require_once 'Zend/Pdf/ElementFactory.php';
                $decodingObjFactory = ElementFactory\ElementFactory::createFactory(1);
                $decodingStream = $decodingObjFactory->newStreamObject($imageData);
                $decodingStream->dictionary->Filter      = new Element\Name('FlateDecode');
                $decodingStream->dictionary->DecodeParms = new Element\Dictionary();
                $decodingStream->dictionary->DecodeParms->Predictor        = new Element\Numeric(15);
                $decodingStream->dictionary->DecodeParms->Columns          = new Element\Numeric($width);
                $decodingStream->dictionary->DecodeParms->Colors           = new Element\Numeric(2);   //GreyAlpha
                $decodingStream->dictionary->DecodeParms->BitsPerComponent = new Element\Numeric($bits);
                $decodingStream->skipFilters();

                $pngDataRawDecoded = $decodingStream->value;

                //Iterate every pixel and copy out gray data and alpha channel (this will be slow)
                for($pixel = 0, $pixelcount = ($width * $height); $pixel < $pixelcount; $pixel++) {
                    $imageDataTmp .= $pngDataRawDecoded[($pixel*2)];
                    $smaskData .= $pngDataRawDecoded[($pixel*2)+1];
                }
                $compressed = false;
                $imageData  = $imageDataTmp; //Overwrite image data with the gray channel without alpha
                break;

            case Png::PNG_CHANNEL_RGB_ALPHA:
                /*
                 * To decode PNG's with alpha data we must create two images from one. One image will contain the RGB data
                 * the other will contain the Gray transparency overlay data. The former will become the object data and the latter
                 * will become the Shadow Mask (SMask).
                 */
                if($bits > 8) {
                    require_once 'Zend/Pdf/Exception.php';
                    throw new Pdf\Exception("Alpha PNGs with bit depth > 8 are not yet supported");
                }

                $colorSpace = new Element\Name('DeviceRGB');

                require_once 'Zend/Pdf/ElementFactory.php';
                $decodingObjFactory = ElementFactory\ElementFactory::createFactory(1);
                $decodingStream = $decodingObjFactory->newStreamObject($imageData);
                $decodingStream->dictionary->Filter      = new Element\Name('FlateDecode');
                $decodingStream->dictionary->DecodeParms = new Element\Dictionary();
                $decodingStream->dictionary->DecodeParms->Predictor        = new Element\Numeric(15);
                $decodingStream->dictionary->DecodeParms->Columns          = new Element\Numeric($width);
                $decodingStream->dictionary->DecodeParms->Colors           = new Element\Numeric(4);   //RGBA
                $decodingStream->dictionary->DecodeParms->BitsPerComponent = new Element\Numeric($bits);
                $decodingStream->skipFilters();

                $pngDataRawDecoded = $decodingStream->value;

                //Iterate every pixel and copy out rgb data and alpha channel (this will be slow)
                for($pixel = 0, $pixelcount = ($width * $height); $pixel < $pixelcount; $pixel++) {
                    $imageDataTmp .= $pngDataRawDecoded[($pixel*4)+0] . $pngDataRawDecoded[($pixel*4)+1] . $pngDataRawDecoded[($pixel*4)+2];
                    $smaskData .= $pngDataRawDecoded[($pixel*4)+3];
                }

                $compressed = false;
                $imageData  = $imageDataTmp; //Overwrite image data with the RGB channel without alpha
                break;

            default:
                require_once 'Zend/Pdf/Exception.php';
                throw new Pdf\Exception( "PNG Corruption: Invalid color space." );
        }

        if(empty($imageData)) {
            require_once 'Zend/Pdf/Exception.php';
            throw new Pdf\Exception( "Corrupt PNG Image. Mandatory IDAT chunk not found." );
        }

        $imageDictionary = $this->_resource->dictionary;
        if(!empty($smaskData)) {
            /*
             * Includes the Alpha transparency data as a Gray Image, then assigns the image as the Shadow Mask for the main image data.
             */
            $smaskStream = $this->_objectFactory->newStreamObject($smaskData);
            $smaskStream->dictionary->Type             = new Element\Name('XObject');
            $smaskStream->dictionary->Subtype          = new Element\Name('Image');
            $smaskStream->dictionary->Width            = new Element\Numeric($width);
            $smaskStream->dictionary->Height           = new Element\Numeric($height);
            $smaskStream->dictionary->ColorSpace       = new Element\Name('DeviceGray');
            $smaskStream->dictionary->BitsPerComponent = new Element\Numeric($bits);
            $imageDictionary->SMask = $smaskStream;

            // Encode stream with FlateDecode filter
            $smaskStreamDecodeParms = array();
            $smaskStreamDecodeParms['Predictor']        = new Element\Numeric(15);
            $smaskStreamDecodeParms['Columns']          = new Element\Numeric($width);
            $smaskStreamDecodeParms['Colors']           = new Element\Numeric(1);
            $smaskStreamDecodeParms['BitsPerComponent'] = new Element\Numeric(8);
            $smaskStream->dictionary->DecodeParms  = new Element\Dictionary($smaskStreamDecodeParms);
            $smaskStream->dictionary->Filter       = new Element\Name('FlateDecode');
        }

        if(!empty($transparencyData)) {
            //This is experimental and not properly tested.
            $imageDictionary->Mask = new Element\Array($transparencyData);
        }

        $imageDictionary->Width            = new Element\Numeric($width);
        $imageDictionary->Height           = new Element\Numeric($height);
        $imageDictionary->ColorSpace       = $colorSpace;
        $imageDictionary->BitsPerComponent = new Element\Numeric($bits);
        $imageDictionary->Filter       = new Element\Name('FlateDecode');

        $decodeParms = array();
        $decodeParms['Predictor']        = new Element\Numeric(15); // Optimal prediction
        $decodeParms['Columns']          = new Element\Numeric($width);
        $decodeParms['Colors']           = new Element\Numeric((($color==Png::PNG_CHANNEL_RGB || $color==Png::PNG_CHANNEL_RGB_ALPHA)?(3):(1)));
        $decodeParms['BitsPerComponent'] = new Element\Numeric($bits);
        $imageDictionary->DecodeParms  = new Element\Dictionary($decodeParms);

        //Include only the image IDAT section data.
        $this->_resource->value = $imageData;

        //Skip double compression
        if ($compressed) {
            $this->_resource->skipFilters();
        }
    }

    /**
     * Image width
     */
    public function getPixelWidth() {
    return $this->_width;
    }

    /**
     * Image height
     */
    public function getPixelHeight() {
        return $this->_height;
    }

    /**
     * Image properties
     */
    public function getProperties() {
        return $this->_imageProperties;
    }
}
