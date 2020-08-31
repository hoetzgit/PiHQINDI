# PiHQINDI
## INDI driver for the Raspberry Pi HQ Camera

This INDI driver is based on the ASCOM driver written by Rob Musquetier available at: https://www.musquetier.nl/downloads/RPiCameraV3_Setup.exe. Many thanks for use of his PHP driver to help refine what parameters are required and providing a test platform. While this driver doesn't use the PHP driver it was very helpiing. 

This driver is an enhanced version of indi-picam that supported previous versions of the camera. It has currently only been tested with the new Pi High Quality camera. I will test it with the older cameras as time permits.

This driver is part of a project to build an INDI based Plate Solver (similar to the Celestron StarSense) that will provide platesolve based alignment and position refinement services to telescopes using INDI.

Parameter	| Default Value	| Options
--------- | ------------- | -------
type|jpg|jpg, bmp
analog_gain|1|1 - 12
exposure|off|all options mentioned on the referred camera webpage are available
flicker|off|off, auto, 50hz, 60hz
awb|auto|off, auto, sun, cloud, shade, tungsten, fluorescent, incandescent, flash, horizon, greyworld
hflip|false|false, true
vflip|false|false, true
binning|1|1, 2, 3, 4
shutter|1000000|1 - 239000000 (uSec.)
drc|off|off,low,medium,high
ag|1|1 - 16
dg|1|1 - 255
mode|3|1, 2, 3, 4
verbose|false|false, true
