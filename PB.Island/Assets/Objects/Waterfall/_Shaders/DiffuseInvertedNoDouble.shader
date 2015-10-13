// Upgrade NOTE: replaced 'PositionFog()' with multiply of UNITY_MATRIX_MVP by position
// Upgrade NOTE: replaced 'V2F_POS_FOG' with 'float4 pos : SV_POSITION'

Shader "Diffuse inverted" {
Properties {
	_Color ("Main Color", Color) = (1,1,1,1)
}

Category {
	/* Upgrade NOTE: commented out, possibly part of old style per-pixel lighting: Blend AppSrcAdd AppDstAdd */
	Fog { Color [_AddFog] }
	Cull Front
	
	// ------------------------------------------------------------------
	// ARB fragment program
	
	#warning Upgrade NOTE: SubShader commented out; uses Unity 2.x per-pixel lighting. You should rewrite shader into a Surface Shader.
/*SubShader {
		// Ambient pass
		Pass {
			Name "BASE"
			Tags {"LightMode" = "Always" /* Upgrade NOTE: changed from PixelOrNone to Always */}
			Color [_PPLAmbient]
			SetTexture [_MainTex] {constantColor [_Color] Combine primary, constant}
		}
		// Vertex lights
		Pass { 
			Name "BASE"
			Tags {"LightMode" = "Vertex"}
			Lighting On
			Material {
				Diffuse [_Color]
				Emission [_PPLAmbient]
			}
			SetTexture [_MainTex] { constantColor [_Color] Combine primary, constant}
		}
		// Pixel lights
		Pass {
			Name "PPL"
			Tags { "LightMode" = "Pixel" }
CGPROGRAM
// Upgrade NOTE: excluded shader from DX11 and Xbox360; has structs without semantics (struct v2f members normal,lightDir)
#pragma exclude_renderers d3d11 xbox360
// Upgrade NOTE: excluded shader from Xbox360; has structs without semantics (struct v2f members normal,lightDir)
#pragma exclude_renderers xbox360
#pragma vertex vert
#pragma fragment frag
#pragma multi_compile_builtin_noshadows
#pragma fragmentoption ARB_fog_exp2
#pragma fragmentoption ARB_precision_hint_fastest
#include "UnityCG.cginc"
#include "AutoLight.cginc"

struct v2f {
	float4 pos : SV_POSITION;
	LIGHTING_COORDS
	float3	normal;
	float3	lightDir;
};

v2f vert (appdata_base v)
{
	v2f o;
	o.pos = mul (UNITY_MATRIX_MVP, v.vertex);
	o.normal = v.normal;
	o.lightDir = ObjSpaceLightDir( v.vertex );
	TRANSFER_VERTEX_TO_FRAGMENT(o);
	return o;
}

float4 frag (v2f i) : COLOR
{
	float3 normal = i.normal;		
	return DiffuseLight( i.lightDir, normal, half4(0.5,0.5,0.5,0.5), LIGHT_ATTENUATION(i) );
}
ENDCG
		}
	}*/
		
	// ------------------------------------------------------------------
	// Radeon 7000
	
	Category {
		Material {
			Diffuse [_Color]
			Emission [_PPLAmbient]
		}
		Lighting On
		#warning Upgrade NOTE: SubShader commented out; uses Unity 2.x style fixed function per-pixel lighting. Per-pixel lighting is not supported without shaders anymore.
/*SubShader {
			// Ambient pass
			Pass {
				Name "BASE"
				Tags {"LightMode" = "Always" /* Upgrade NOTE: changed from PixelOrNone to Always */}
				Color [_PPLAmbient]
				Lighting Off
				SetTexture [_MainTex] {Combine primary, primary}
			}
			// Vertex lights
			Pass { 
				Name "BASE"
				Tags {"LightMode" = "Vertex"}
				Lighting On
				Material {
					Diffuse [_Color]
					Emission [_PPLAmbient]
				}
				SetTexture [_MainTex] {Combine primary, texture}
			}
			// Pixel lights with 2 light textures
			Pass {
				Name "PPL"
				Tags {
					"LightMode" = "Pixel"
					"LightTexCount"  = "2"
				}
				ColorMask RGB
				SetTexture [_LightTexture0]  { combine previous * texture alpha, previous }
				SetTexture [_LightTextureB0] { combine previous * texture alpha, previous }
			}
			// Pixel lights with 1 light texture
			Pass {
				Name "PPL"
				Tags {
					"LightMode" = "Pixel"
					"LightTexCount"  = "1"
				}
				ColorMask RGB
				SetTexture [_LightTexture0] { combine previous * texture alpha, previous }
			}
			// Pixel lights with 0 light textures
			Pass {
				Name "PPL"
				Tags {
					"LightMode" = "Pixel"
					"LightTexCount" = "0"
				}
				ColorMask RGB
				SetTexture[_MainTex] { combine previous }
			}
		}*/
	}
}

Fallback "VertexLit", 2

}
