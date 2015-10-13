using UnityEngine;
using System.Collections;

public class SAHelper 
{

    public class NearClipPoints
    {
        public Vector3 LowerLeft;
        public Vector3 UpperLeft;
        public Vector3 LowerRight;
        public Vector3 UpperRight;

    };

    public static NearClipPoints GetCameraNearClipPoints(Vector3 cameraPos)
    {
        NearClipPoints points = new NearClipPoints();

        Camera cam = Camera.main;
        if (cam == null)
        {
            return points;
        }

        float distance = cam.nearClipPlane;
        float halfFOV = cam.fov * 0.5f * Mathf.Deg2Rad;
        float height = Mathf.Tan(halfFOV) * distance;
        float width = height * cam.aspect;

        points.LowerRight = (cameraPos + cam.transform.right * width) - (cam.transform.up * height) + (cam.transform.forward * distance);
        points.LowerLeft  = (cameraPos - cam.transform.right * width) - (cam.transform.up * height) + (cam.transform.forward * distance);
        points.UpperRight = (cameraPos + cam.transform.right * width) + (cam.transform.up * height) + (cam.transform.forward * distance);
        points.UpperLeft  = (cameraPos - cam.transform.right * width) + (cam.transform.up * height) + (cam.transform.forward * distance);

        return points;
    }

    public static float ClampAngle(float angle, float min, float max)
    {
        do
        {
            if (angle > 360)
            {
                angle -= 360;
            }
            if (angle < -360)
            {
                angle += 360;
            }
        } while (angle < -360 && angle > 360);

        return Mathf.Clamp(angle, min, max);
    }

    public static float CalcDistance(Vector3 from, Vector3 to)
    {
        return Mathf.Abs(Vector3.Distance(new Vector3(from.x, 0f, from.z), new Vector3(to.x, 0f, to.z)));
    }

    public static float CalcAngle(Quaternion from, Quaternion to)
    {
        return Quaternion.Angle(from, to);
    }
}
