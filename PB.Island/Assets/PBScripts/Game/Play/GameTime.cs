using UnityEngine;
using System.Collections;
using System.Collections.Generic;

public class GameTime : MonoBehaviour
{
    #region MemVars & Props

    public static GameTime gameTime = null;

	protected List<Transform> suns;				// The sun lights
	protected float dayCycleInSeconds = 60;	// How long a day will be in seconds
	protected float StartTimeInHour = 0;	// The starting time (LILOGMT+?)
	protected float sunRiseInSeconds = 10;
	protected float sunSetInSeconds = 50;
	protected float sunNoonInSeconds = 30;
	
	enum DayTime 
    {
		SunIdle,
		SunRise,
		SunNoon,
		SunSet
	}
	
	DayTime _dayTime = DayTime.SunIdle;
	
	private const float SECOND = 1;		
	private const float MINUTE = 60 * SECOND;
	private const float HOUR = 60 * MINUTE;
	private const float DAY = 24 * HOUR;
	
	private float _timeOfDay = 0;
	private float _degreePerSecond;		// How many degree the sun should rotate per second
    private System.Timers.Timer _timer;

    #endregion


    #region Mono Methods

    void Start() 
    {
        SetupSky();

        // Ticker for Game Time
        _timer = new System.Timers.Timer(1000);
        _timer.Elapsed += new System.Timers.ElapsedEventHandler(_timer_Elapsed);
        _timer.Start();
	}

    void Update()
    {
    }

    #endregion 


    #region Methods

    void _timer_Elapsed(object sender, System.Timers.ElapsedEventArgs e)
    {
        PBGameMaster.GameTime = PBGameMaster.GameTime + System.TimeSpan.FromSeconds(1);
    }
		
    private void SetupSky()
    {
        _degreePerSecond = 360.0f / dayCycleInSeconds;
        _timeOfDay = 0;

        suns = new List<Transform>();

        Object[] lights = FindObjectsOfType(typeof(Light));
        foreach (Object obj in lights)
        {
            Light light = obj as Light;
            if (light.type == LightType.Directional)
            {
                suns.Add(light.transform);
            }
        }

        //RenderSettings.skybox.SetFloat("_Blend", 0);

        //StartTimeAtHour(StartTimeInHour);	// Set the default time
    }

    private void UpdateSky()
    {

        /*
        UpdateSunRotation();
		
        _timeOfDay += Time.deltaTime;
		
        if (_timeOfDay > dayCycleInSeconds)
        {
            _timeOfDay -= dayCycleInSeconds;
        }
		
        if (_timeOfDay >= sunRiseInSeconds && _timeOfDay <= sunNoonInSeconds)
        {
            _dayTime = GameTime.DayTime.SunRise;
        }
        else if (_timeOfDay > sunNoonInSeconds && _timeOfDay < sunSetInSeconds)
        {
            _dayTime = GameTime.DayTime.SunSet;
        }
        else
        {
            _dayTime = GameTime.DayTime.SunIdle;
        }
         
        SkyBlend();
    
         */
    }

	private void StartTimeAtHour(float hour)
	{
		float tod = (hour / 24) * dayCycleInSeconds;	// Convert hour into time of day in one day cycle 
		
		float rotation = (_degreePerSecond * tod);
		
		foreach (Transform sunTransform in suns)	
		{
			sunTransform.Rotate(new Vector3(rotation, 0, 0));
		}
	}
	
	private void UpdateSunRotation()
	{
		foreach (Transform sunTransform in suns)	
		{
			Vector3 rotation = new Vector3(_degreePerSecond, 0, 0) * Time.deltaTime;
			sunTransform.Rotate(rotation);
		}
	}
	
	private void SkyBlend()
	{
		float blendFactor = 0;
		
		switch (_dayTime)
		{
		case DayTime.SunRise:
				blendFactor = (_timeOfDay - sunRiseInSeconds) / (sunNoonInSeconds - sunRiseInSeconds);
			break;
			
		case DayTime.SunSet:
				blendFactor = (_timeOfDay - sunNoonInSeconds) / (sunSetInSeconds - sunNoonInSeconds);
				blendFactor = 1 - blendFactor;
			break;
		}
		
		RenderSettings.skybox.SetFloat("_Blend", blendFactor);
    }

    #endregion

}
