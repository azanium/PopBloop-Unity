using UnityEngine;
using System.Collections;
using System.Collections.Generic;
using System;
using PB.Client;

public class WelcomeController : UITableViewController<Brands.Brand>
{
    #region MemVars & Props

    private static WelcomeController welcomeController;

    public GameObject gotoProfile;
    public GameObject gotoMyAvatar;
    public GameObject startButton;
    public GameObject facebookButton;

    public override bool StackPushable
    {
        get
        {
            return false;
        }
    }


    #endregion


    #region Mono Methods

    private void EnableStartButton(bool state)
    {
        NGUITools.SetActive(facebookButton, !state);
        NGUITools.SetActive(startButton, state);
    }

    public override void Start()
    {
        welcomeController = this;
        Debug.Log(Application.persistentDataPath);

        if (Application.platform == RuntimePlatform.WindowsEditor || Application.platform == RuntimePlatform.OSXEditor)
        {
            EnableStartButton(true);
        }
        else
        {
            EnableStartButton(FacebookConnect.IsSessionValid());
        }

    }

    public override void OnEnable()
    {
        base.OnEnable();

        DressRoom.HideCharacter();

        TabMenuController.ShowNavigator(false);

        SocialNetworkingManager.facebookLogin += SocialNetworkingManager_facebookLogin;
    }

    public override void OnDisable()
    {
        base.OnDisable();
        SocialNetworkingManager.facebookLogin -= SocialNetworkingManager_facebookLogin;
    }

    void SocialNetworkingManager_facebookLogin()
    {
        Debug.LogWarning("Logged In");
    }

    #endregion


    #region Internal & Public Methods

    private void DisableButton(GameObject button, bool state)
    {
        var btn = button.GetComponentInChildren<UIButton>();
        if (btn != null)
        {
            btn.isEnabled = state;
        }
    }

    private bool FacebookLogin()
    {
        var defaultProfile = PBDefaults.GetProfile(KPConstants.KPSettings);

        bool result = FacebookConnect.Login();

        DisableButton(facebookButton, false);
        
        if (result)
        {
            Debug.Log("FacebookConnect: logging in");
            //bool success = true;
            //string email = "syuaibi@gmail.com";
            FacebookConnect.GetEmail(startButton, (success, email) =>
            {
                Debug.Log("FacebookConnect: GetEmail");
                if (success)
                {
                    defaultProfile.SetString(KPConstants.KPEmail, email);

                    DressRoom.CallUserApi(DressRoom.UserApiRequest.UserCheck, email, (dic) =>
                        {
                            Debug.Log("FacebookLogin: Found user on the Server:" + email);

                            bool isUserExist = (bool)dic["valid"];

                            if (isUserExist)
                            {
                                DressRoom.CallUserApi(DressRoom.UserApiRequest.GetProfile, email, (profile) =>
                                    {
                                        Debug.Log("FacebookLogin: Got User Profile ");
                                        bool userValid = (bool)profile["valid"];
                                        if (userValid)
                                        {
                                            Debug.Log("user is valid profile");
                                            defaultProfile.SetBool(KPConstants.KPLoggedIn, true);
                                            defaultProfile.SetString(KPConstants.KPUsername, (string)profile["username"]);
                                            defaultProfile.SetString(KPConstants.KPAvatarname, (string)profile["avatarname"]);
                                            defaultProfile.SetString(KPConstants.KPUserStatus, (string)profile["state_of_mind"]);
                                            defaultProfile.SetString(KPConstants.KPPlayerId, (string)profile["lilo_id"]);

                                            //ShowMainPage();
                                            Debug.Log("Ready");
                                            
                                            EnableStartButton(true);
                                        }
                                        else
                                        {
                                            Debug.Log("user is invalid profile");
                                            ShowProfile();
                                        }

                                    });
                            }
                            else
                            {
                                Debug.Log("FacebookLogin: User not exists on the Server");
                                ShowProfile();
                            }
                        });
                }
                else
                {
                    Debug.LogWarning("FacebookConnect: Failed to get user's email from Facebook: " + email);
                }
            });
        }

        return result;
    }

    public void ShowMainPage()
    {
        UINavigationController.PushController(typeof(MyAvatarController));

        TabMenuController.ShowNavigator(true);
    }

    public void ShowProfile()
    {
        // User have to fill out their profile
        Debug.Log("Edit Profile");
        UINavigationController.PushController(typeof(ProfileController));
    }

    public static void GotoProfile()
    {
        if (welcomeController != null)
        {
            welcomeController.ShowProfile();
        }
    }

    public static void GotoMainPage()
    {
        if (welcomeController != null)
        {
            welcomeController.ShowMainPage();
        }
    }

    private void OnStart3()
    {
        ShowMainPage();
    }

    private void OnStart()
    {
        Debug.Log("Start");

        var profile = PBDefaults.GetProfile(KPConstants.KPSettings);
        var loggedIn = profile.GetBool(KPConstants.KPLoggedIn);

        if (Application.platform == RuntimePlatform.WindowsEditor || Application.platform == RuntimePlatform.OSXEditor)
        {
			profile.SetString(KPConstants.KPEmail, "syuaibi@gmail.com");
            ShowMainPage();
            return;
        }

        if (!loggedIn)
        {
            FacebookLogin();
        }
        else
        {
            var email = profile.GetString(KPConstants.KPEmail);
            
            if (string.IsNullOrEmpty(email))
            {
                FacebookLogin();
            }
            else
            {
                ShowMainPage();
            }
        }

    }

    private void OnFacebookConnect()
    {
        if (FacebookConnect.Login())
        {
            FacebookLogin();
        }

    }

    #endregion
}
