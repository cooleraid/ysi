void mainImage( out vec4 fragColor, in vec2 fragCoord )
{
    vec2 uv = (fragCoord*2.0-iResolution.xy)/iResolution.y;
    // fill the screen with white color
   	fragColor = vec4(1,1,1,1);
	// fill the top left square box with red color 
    if(uv.x<-0.75 && uv.y>0.2)
    fragColor = vec4(1,0,0,0);
    
    // fill the top right rectangle with red color 
    if(uv.x>-0.35 && uv.y>0.2)
    fragColor = vec4(1,0,0,0);
    
  	// fill the bottom left square box with red color 
    if(uv.x<-0.75 && uv.y<-0.2)
    fragColor = vec4(1,0,0,0);
    
    // fill the bottom right rectangle with red color 
    if(uv.x>-0.35 && uv.y<-0.2)
    fragColor = vec4(1,0,0,0);
    
    // fill the vertical rectangle with blue color 
    if(uv.x>-0.65 && uv.x<-0.45)
    fragColor = vec4(0,0,1,0);
    
    // fill the horizontal rectangle with blue color 
    if(uv.y>-0.1 && uv.y<0.1)
    fragColor = vec4(0,0,1,0);
}