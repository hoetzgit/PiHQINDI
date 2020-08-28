<html>    
    <head>    
        <title>PIHQCCD Manual Image Form</title>    
    </head>    
    <body>    
        <link href = "pihdccd.css" type = "text/css" rel = "stylesheet" />    
        <H1>PIHQCCD Manual Image Form</H1>
        <h2>Enter image parameters</h2>    
        <form name = "form1" action="PiHQINDI.php" method = "post" enctype = "multipart/form-data" >    
            <div class = "container">    
                <div class = "form_group">    
                    <label>Debug:</label>    
                    <input type = "text" name = "debug" value = "1" required/>    
                </div>    
                <div class = "form_group">    
                    <label>Do It:</label>    
                    <input type = "text" name = "doit" value = "1" required />    
                </div>    
                <div class = "form_group">    
                    <label>Image type:</label>    
                    <select name = "type" id = "type">
                            <option value="jpg">JPG</option>
                            <option value="png">PNG</option>
                            <option value="gif">GIF</option>
                            <option value="bmp">BMP</option>
                            </select>
                </div>    
                <div class = "form_group">    
                    <label>Exposure:</label>    
                    <select name = "exposure" id = "exposure">
                            <option value="off">off</option>
                            <option value="auto">auto</option>
                            <option value="night">night</option>
                            <option value="nightpreview">nightpreview</option>
                            <option value="verylong">verylong</option>
                            <option value="fireworks">fireworks</option>
                            </select>    
                </div>   
                <div class = "form_group">    
                    <label>Analog Gain:</label>    
                    <input type = "text" name = "analog_gain" value = "1" required/>    
                </div>  
                <div class = "form_group">    
                    <label>Flicker:</label>    
                    <select name = "flicker" id = "flicker">  
                            <option value="off">OFF</option>
                            <option value="auto">AUTO</option>
                            <option value="50hz">50hz</option>
                            <option value="60hz">60hz</option>
                            </select>  
                </div>  
                <div class = "form_group">    
                    <label>AWB:</label>    
                    <select name = "awb" id = "awb">  
                            <option value="off">OFF</option>
                            <option value="auto">AUTO</option>
                            <option value="sun">sun</option>
                            <option value="cloud">cloud</option>
                            <option value="shade">shade</option>
                            <option value="tungsten">tungsten</option>
                            <option value="fluorescent">fluorescent</option>
                            <option value="incandescent">incandescent</option>
                            <option value="flash">flash</option>
                            <option value="horizon">horizon</option>
                            <option value="greyworld">greworld</option>
                            </select>  
                </div>
                <div class = "form_group">    
                    <label>hflip:</label>    
                    <select name = "hflip" id = "hflip">  
                            <option value="false">false</option>
                            <option value="true">true</option>
                    </select>  
                </div>
                <div class = "form_group">    
                    <label>vflip:</label>    
                    <select name = "vflip" id = "vflip">  
                            <option value="false">false</option>
                            <option value="true">true</option>
                    </select>  
                </div>
                <div class = "form_group">    
                    <label>roi_x:</label>    
                    <input type = "text" name = "roi_x" value = "-1" required/>  
                </div>
                <div class = "form_group">    
                    <label>roi_x:</label>    
                    <input type = "text" name = "roi_y" value = "-1" required/>  
                </div>
                <div class = "form_group">    
                    <label>roi_w:</label>    
                    <input type = "text" name = "roi_w" value = "-1" required/>  
                </div>
                <div class = "form_group">    
                    <label>Shutter:</label>    
                    <input type = "text" name = "shutter" value = "1000000" required/>  
                </div>
                <div class = "form_group">    
                    <label>Dynamic Range Control:</label>    
                    <select name = "drc" id = "drc">  
                            <option value="off">off</option>
                            <option value="low">low</option>
                            <option value="med">med</option>
                            <option value="high">high</option>
                    </select>  
                </div> 
                 <div class = "form_group">    
                    <label>Analog Gain:</label>    
                    <input type = "text" name = "ag" value = "1.0" required/>    
                </div>    
                <div class = "form_group">    
                    <label>Digital Gain:</label>    
                    <input type = "text" name = "dg" value = "1.0" required/>    
                </div>                 
                <div class = "form_group">    
                    <label>Binning:</label>    
                    <select name = "binning" id = "binning">  
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                    </select>  
                </div> 
                <div class = "form_group">    
                    <label>Annotate:</label>    
                    <input type = "text" name = "annotate" value = "0" required/>    
                </div> 
                <div class = "form_group">    
                    <label>Timeout:</label>    
                    <input type = "text" name = "timeout" value = "100" required/>    
                </div> 
                <div class = "form_group">   
                    <label>Verbose:</label> 
                    <select name = "verbose" id = "verbose">  
                            <option value="false">false</option>
                            <option value="true">true</option>
                    </select>  
                </div> 
                <div class = "form_group">   
                    <label>Convert to FITS:</label> 
                    <select name = "convert" id = "convert">  
                            <option value="false">false</option>
                            <option value="true">true</option>
                    </select>  
                </div> 
                <div class = "form_group"> 
                    <input type="submit" value="Submit">
                    </div>
            </div>    
        </form>    
    </body>    
</html>   
