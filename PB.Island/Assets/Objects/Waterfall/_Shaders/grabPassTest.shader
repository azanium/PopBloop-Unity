Shader "Grab Pass" 
{
	Properties 
	{
		_Color ("Main Color", Color) = (1,1,1,1)
		_MainTex ("Base (RGB)", 2D) = "white" {}
	}
	
	SubShader 
	{
		GrabPass 
		{							
			Name "BASE"
			Tags { "LightMode" = "Always" }
 		}
 		
 		Pass
 		{
 			SetTexture [_GrabTexture] // Texture we grabbed in the pass above
 			{
 				constantColor [_Color]
 				Combine constant * Texture
 			}	
 			
 		}
 		
 		
 		Pass 
 		{
           SetTexture [_MainTex] 
           {
               Combine Previous * Texture
           }
       	}
 		
		//UsePass "Self-Illumin/VertexLit/BASE"
	} 
	
	FallBack "Diffuse", 1
}
