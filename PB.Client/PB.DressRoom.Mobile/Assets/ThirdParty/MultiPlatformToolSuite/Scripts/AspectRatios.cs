using UnityEngine;
using System.Collections;

public enum AspectRatio
{
	Aspect4by3 = 43,
	Aspect5by4 = 54,
	Aspect5by3 = 53,
	Aspect16by9 = 169,
	Aspect16by10 = 1610,
	Aspect3by2 = 32,
	AspectCustom1024x600 = 1024600,
	AspectCustom800x480 = 800480,
	AspectOthers = 0
}

public class AspectRatios : MonoBehaviour
{
	//Tolerance of calculated ratio to exact ratio
	const float ratioTolerance = 0.03f;
	
	const float aspect4By3Ratio = 4f / 3f;
	const float aspect5by4Ratio = 5f / 4f;
	const float aspect5by3Ratio = 5f / 3f;
	const float aspect16By9Ratio = 16f / 9f;
	const float aspect16By10Ratio = 16f / 10f;
	const float aspect3by2Ratio = 3f / 2f;
	
	//These are currently the custom Unity Android resolutions that don't fit into a standard aspect ratio category
	const float aspectCustom1024x600 = 1024f / 600f;
	const float aspectCustom800x480 = 800f / 480f;
	
	#if UNITY_EDITOR
	// NOTE: this is a hack.  When "Screen.width/height" is called from an editor script, it returns the wrong size, so we call this instead.
	internal static void getScreenSizeHack(out float width, out float height)
	{
	    System.Type T = System.Type.GetType("UnityEditor.GameView,UnityEditor");
	    System.Reflection.MethodInfo GetSizeOfMainGameView = T.GetMethod("GetSizeOfMainGameView",System.Reflection.BindingFlags.NonPublic | System.Reflection.BindingFlags.Static);
	    System.Object rez = GetSizeOfMainGameView.Invoke(null,null);
	    var rezVec = (Vector2)rez;
	    width = rezVec.x;
	    height = rezVec.y;
	}
	
	public static bool UseEditorResolutionHack;
	#endif

	public static AspectRatio GetAspectRatio ()
	{
		float currentWidth = Screen.width;
		float currentHeight = Screen.height;
		#if UNITY_EDITOR
		if (UseEditorResolutionHack) getScreenSizeHack(out currentWidth, out currentHeight);
		#endif
		
		//Calculate aspect ratio as a float
		float calculatedAspectRatio = currentWidth / currentHeight;
		
		//check for custom resolutions (usually Android) that don't fit a standard aspect ratio category
		if(currentWidth == 1024 && currentHeight == 600)
			return AspectRatio.AspectCustom1024x600;
		else if (currentWidth == 800 && currentHeight == 480)
			return AspectRatio.AspectCustom800x480;
		
		//check for the resular aspect ratios
		else if (Mathf.Abs (calculatedAspectRatio - aspect4By3Ratio) < ratioTolerance)
			return AspectRatio.Aspect4by3;
		else if (Mathf.Abs (calculatedAspectRatio - aspect5by4Ratio) < ratioTolerance)
			return AspectRatio.Aspect5by4;
		else if (Mathf.Abs (calculatedAspectRatio - aspect5by3Ratio) < ratioTolerance)
			return AspectRatio.Aspect5by3;
		else if (Mathf.Abs (calculatedAspectRatio - aspect16By9Ratio) < ratioTolerance)
			return AspectRatio.Aspect16by9;
		else if (Mathf.Abs (calculatedAspectRatio - aspect16By10Ratio) < ratioTolerance)
			return AspectRatio.Aspect16by10;
		else if (Mathf.Abs (calculatedAspectRatio - aspect3by2Ratio) < ratioTolerance)
			return AspectRatio.Aspect3by2;
		
		//we haven't matched an exact aspect ratio so lets find the closest one!
		else
			return FindNearestAspectRatio (calculatedAspectRatio);
	}

	static AspectRatio FindNearestAspectRatio (float calculatedAspectRatio)
	{	
		float nearestRatio = float.MinValue;
		float closestFoundSoFar = float.MaxValue;
		float[] ratios = {aspect4By3Ratio, aspect5by4Ratio, aspect5by3Ratio, aspect16By9Ratio, aspect16By10Ratio, aspect3by2Ratio };
		
		for (int i = 0; i < ratios.Length; i++) {
			float dist = Mathf.Abs(calculatedAspectRatio - ratios[i]);
			if (dist < closestFoundSoFar){
				nearestRatio = ratios[i];
				closestFoundSoFar = dist;
			}
		}
		
		//return the closest aspect ratio
		if(nearestRatio == aspect4By3Ratio) 
			return AspectRatio.Aspect4by3;
		else if(nearestRatio == aspect5by4Ratio) 
			return AspectRatio.Aspect5by4;
		else if(nearestRatio == aspect5by3Ratio) 
			return AspectRatio.Aspect5by3;
		else if(nearestRatio == aspect16By9Ratio) 
			return AspectRatio.Aspect16by9;
		else if(nearestRatio == aspect16By10Ratio) 
			return AspectRatio.Aspect16by10;
		else if(nearestRatio == aspect3by2Ratio)
			return AspectRatio.Aspect3by2;
		else 
			return AspectRatio.AspectOthers;
	}
}
