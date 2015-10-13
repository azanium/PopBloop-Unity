using UnityEngine;
using System.Collections;
using System.Collections.Generic;

/// <summary>
/// Navigation Controller Class
/// </summary>
public class UINavigationController : MonoBehaviour 
{
    #region MemVars & Props

    public static UINavigationController navigationController;

    public class NavigationData
    {
        public UIViewController viewController;
        public string parameter;
        public NavigationData(UIViewController controller, string param)
        {
            viewController = controller;
            parameter = param;
        }
    }

    public delegate void ControllerDelegate(UIViewController controller);

    /// <summary>
    /// View Controller's Stack
    /// </summary>
    private Stack<NavigationData> controllerStacks = new Stack<NavigationData>();

    /// <summary>
    /// Detected View Controllers
    /// </summary>
    public System.Collections.Generic.List<UIViewController> viewControllers = new List<UIViewController>();

    /// <summary>
    /// Currently visible View Controller;
    /// </summary>
    public UIViewController currentController;

    /// <summary>
    /// First View Controller to be activated
    /// </summary>
    public List<UIViewController> firstActiveController = new List<UIViewController>();

    public AnimationClip showForwardAnimation;// = "SlideInLeft";
    public AnimationClip hideForwardAnimation;// = "SlideOutLeft";
    public AnimationClip showBackAnimation;// = "SlideInRight";
    public AnimationClip hideBackAnimation;// = "SlideOutRight";
    
    /// <summary>
    /// Disable all controllers upon startup
    /// </summary>
    public bool disableControllersOnStartup = true;

    /// <summary>
    /// Display exit GUI for back button on root
    /// </summary>
    private GUIStyle displayExitGuiStyle;

    /// <summary>
    /// Required time to exit since user hit the last escape
    /// </summary>
    public float timeOutToQuitApp = 2f;
    private Rect displayRect = new Rect(Screen.width * 0.5f, Screen.height - 160, 200, 100);
    private GUIContent displayExitContent = new GUIContent("Hit back once more to exit KamarPas");
    private bool isDisplayExitGui = false;

    public bool useLogs = false;

    /// <summary>
    /// Map for Controller Path to its view controller
    /// </summary>
    protected Dictionary<string, UIViewController> mapControllerPath = new Dictionary<string, UIViewController>();

    #endregion


    #region Virtual Methods

    public void Awake()
    {
        navigationController = this;
    }

    public void Start()
    {
        displayExitGuiStyle = new GUIStyle();
        displayExitGuiStyle.normal.background = ResourceManager.Instance.LoadTexture2D("GUI/HUD/loading_bg");
        displayExitGuiStyle.font = ResourceManager.Instance.LoadFont("GUI/Fonts/DroidSans");
        displayExitGuiStyle.fontSize = 16;
        displayExitGuiStyle.normal.textColor = Color.white;
        displayExitGuiStyle.padding = new RectOffset(10, 10, 5, 5);

        // Get all objects whether active or not
        UIViewController[] objs = Resources.FindObjectsOfTypeAll(typeof(UIViewController)) as UIViewController[];
        
        foreach (UIViewController view in objs)
        {
            // Selective insertion
            if (viewControllers.Contains(view) == false)
            {
                viewControllers.Add(view);
            }

            // If we are to disable the whole controllers on startup
            if (disableControllersOnStartup)
            {
                NGUITools.SetActive(view.gameObject, false);
            }

            // Auto add Animation component
            if (view.GetComponent<Animation>() == null)
            {
                view.gameObject.AddComponent<Animation>();
            }

            // Add all animations
            if (showForwardAnimation != null)
            {
                view.animation.AddClip(showForwardAnimation, showForwardAnimation.name);
            }
            
            if (hideForwardAnimation != null)
            {
                view.animation.AddClip(hideForwardAnimation, hideForwardAnimation.name);
            }
            
            if (showBackAnimation != null)
            {
                view.animation.AddClip(showBackAnimation, showBackAnimation.name);
            }

            if (hideBackAnimation != null)
            {
                view.animation.AddClip(hideBackAnimation, hideBackAnimation.name);
            }

            if (view.showForwardAnimation != null)
            {
                view.animation.AddClip(view.showForwardAnimation, view.showForwardAnimation.name);
            }

            if (view.hideForwardAnimation != null)
            {
                view.animation.AddClip(view.hideForwardAnimation, view.hideForwardAnimation.name);
            }

            if (view.showBackAnimation != null)
            {
                view.animation.AddClip(view.showBackAnimation, view.showBackAnimation.name);
            }

            if (view.hideBackAnimation != null)
            {
                view.animation.AddClip(view.hideBackAnimation, view.hideBackAnimation.name);
            }

            if (string.IsNullOrEmpty(view.controllerPath))
            {
                string controllerPath = string.Format("/{0}", view.GetType().ToString());
                int index = controllerPath.ToLower().IndexOf("controller");
                controllerPath = controllerPath.Remove(index, 10);
                view.controllerPath = controllerPath;
                if (mapControllerPath.ContainsKey(controllerPath) == false)
                {
                    mapControllerPath.Add(controllerPath, view);
                }
            }
        }

        // Activate the first controllers
        if (firstActiveController != null)
        {
            foreach (var c in firstActiveController)
            {
                ActivateController(c, true);
            }
        }
    }

    public static string GetControllerPathUrl(string path)
    {
        int index = path.IndexOf("?");
        if (index > -1)
        {
            path = path.Remove(index);
        }

        return path;
    }

    public static Dictionary<string, string> GetControllerPathParams(string path)
    {
        Dictionary<string, string> result = new Dictionary<string, string>();

        int index = path.IndexOf('?');
        if (index < 0)
        {
            return result;
        }

        string subPath = path.Substring(index+1);
        string[] param = subPath.Split('&');

        foreach (string p in param)
        {
            string[] pair = p.Split('=');
            string left = "";
            string right = "";
            if (pair.Length > 0)
            {
                left = pair[0];
            }
            if (pair.Length > 1)
            {
                right = pair[1];
            }

            if (string.IsNullOrEmpty(left) == false)
            {
                result.Add(left, right);
            }

        }

        return result;
    }

    public void Update()
    {
        if (Input.GetKeyDown(KeyCode.Escape))
        {
            if (controllerStacks.Count > 1)
            {
                dismissController();
            }
            else
            {
                if (isDisplayExitGui == false)
                {
                    StartCoroutine(TimeToExit());
                }
                else
                {
                    Debug.LogWarning("Now quitting");
                    Application.Quit();
                }
            }
        }
    }

    private IEnumerator TimeToExit()
    {
        isDisplayExitGui = true;
        yield return new WaitForSeconds(timeOutToQuitApp);
        isDisplayExitGui = false;
    }

    public void OnGUI()
    {
        if (isDisplayExitGui)
        {
            var size = displayExitGuiStyle.CalcSize(displayExitContent);
            displayRect.x = (Screen.width * 0.5f - size.x * 0.5f);
            displayRect.width = size.x;
            displayRect.height = size.y;
            GUI.Label(displayRect, displayExitContent, displayExitGuiStyle);
        }
    }

    #endregion


    #region Public Methods

    public void clearControllers()
    {
        controllerStacks.Clear();
        if (firstActiveController != null)
        {
            foreach (var c in firstActiveController)
            {
                ActivateController(c.GetType(), true);
            }
        }
        mapControllerPath.Clear();
    }

    public static void ClearControllers()
    {
        if (navigationController != null)
        {
            navigationController.clearControllers();
        }
    }

    private void activateController(UIViewController controller, bool state)
    {
        if (viewControllers.Contains(controller))
        {
            NGUITools.SetActive(controller.gameObject, state);

            if (state)
            {
                currentController = controller;
                PushToStack(controller, "");
            }
        }
    }

    private void activateController(System.Type controllerType, bool state)
    {
        foreach (UIViewController controller in viewControllers)
        {
            if (controller.GetType() == controllerType)
            {
                NGUITools.SetActive(controller.gameObject, state);

                if (state)
                {
                    currentController = findControllerByType(controllerType);
                    PushToStack(currentController, "");
                }
                break;
            }
        }
    }

    private void PrintControllerStacks()
    {
        string stack = "";
        foreach (var controller in controllerStacks)
        {
            stack += controller.viewController.name + ", ";
        }

        if (useLogs)
        {
            Debug.LogWarning("Controller Stacks: " + stack);
        }
    }

    /// <summary>
    /// Activate Controller should be called before any of Push or Pop controller
    /// </summary>
    /// <param name="controllerType">The type of the View Controller</param>
    /// <param name="state">true to enable, false otherwise</param>
    public static void ActivateController(System.Type controllerType, bool state)
    {
        if (navigationController != null)
        {
            navigationController.activateController(controllerType, state);
        }
    }

    /// <summary>
    /// Activate View Controller 
    /// Should be called before any push or dismiss controller
    /// </summary>
    /// <param name="controller">The controller we wish to activate</param>
    /// <param name="state">true to enable, false otherwise</param>
    public static void ActivateController(UIViewController controller, bool state)
    {
        if (navigationController != null)
        {
            navigationController.activateController(controller, state);
        }
    }
    /*
    public void pushControllerAsFirst(UIViewController controller, ControllerDelegate onAppear, ControllerDelegate onAppeared)
    {
        AnimationClip showAnimClip, hideAnimClip;
        GetNavigationAnimation(true, out showAnimClip, out hideAnimClip);

        if (controller != null)
        {
            if (currentController != null)
            {
                Animation currentTarget = currentController.GetComponent<Animation>();
                if (currentTarget != null && hideAnimClip != null)
                {
                    var anim = ActiveAnimation.Play(currentTarget, hideAnimClip.name, AnimationOrTween.Direction.Forward, AnimationOrTween.EnableCondition.DoNothing, AnimationOrTween.DisableCondition.DisableAfterForward);
                    currentController.OnDissapear();

                    anim.onFinished = (a) =>
                    {
                        currentController.OnDisappeared();
                    };
                }
            }

            Animation target = controller.GetComponent<Animation>();
            if (target != null && showAnimClip != null)
            {
                ActiveAnimation animation = ActiveAnimation.Play(target, showAnimClip.name, AnimationOrTween.Direction.Forward, AnimationOrTween.EnableCondition.EnableThenPlay, AnimationOrTween.DisableCondition.DoNotDisable);

                if (onAppear != null)
                {
                    onAppear(controller);
                }
                controller.OnAppear();

                animation.onFinished = (anim) =>
                {
                    if (onAppeared != null)
                    {
                        onAppeared(controller);
                    }
                    controller.OnAppeared();
                };
            }

            currentController = controller;

            PushToStack(controller);
        }

        PrintControllerStacks();
    }*/

    public void pushControllerAsFirst(UIViewController controller, ControllerDelegate onAppear, ControllerDelegate onAppeared)
    {
        controllerStacks.Clear();
        pushController(controller, onAppear, onAppeared);
    }

    public void pushControllerAsFirst(System.Type controllerType, ControllerDelegate onAppear, ControllerDelegate onAppeared)
    {
        controllerStacks.Clear();
        pushController(controllerType, onAppear, onAppeared);
    }

    public NavigationData pushController(UIViewController controller, ControllerDelegate onAppear, ControllerDelegate onAppeared)
    {
        AnimationClip showAnimClip = null, hideAnimClip = null;
        if (currentController != null)
        {
            GetNavigationAnimation(currentController, true, out showAnimClip, out hideAnimClip);
        }
        
        NavigationData navData = null;

        if (controller != null)
        {
            if (currentController != null)
            {
                Animation currentTarget = currentController.GetComponent<Animation>();
                if (currentTarget != null && hideAnimClip != null)
                {
                    var anim = ActiveAnimation.Play(currentTarget, hideAnimClip.name, AnimationOrTween.Direction.Forward, AnimationOrTween.EnableCondition.DoNothing, AnimationOrTween.DisableCondition.DisableAfterForward);
                    currentController.OnDissapear();

                    anim.onFinished = (a) =>
                        {
                            currentController.OnDisappeared();
                        };
                }
            }

            Animation target = controller.GetComponent<Animation>();
            if (target != null && showAnimClip != null)
            {
                ActiveAnimation animation = ActiveAnimation.Play(target, showAnimClip.name, AnimationOrTween.Direction.Forward, AnimationOrTween.EnableCondition.EnableThenPlay, AnimationOrTween.DisableCondition.DoNotDisable);

                if (onAppear != null)
                {
                    onAppear(controller);
                }
                controller.OnAppear();

                animation.onFinished = (anim) =>
                {
                    if (onAppeared != null)
                    {
                        onAppeared(controller);
                    }
                    controller.OnAppeared();
                };
            }
            
            currentController = controller;

            navData = PushToStack(controller, "");
        }

        PrintControllerStacks();

        return navData;
    }

    public NavigationData pushController(System.Type controllerType, ControllerDelegate onAppear, ControllerDelegate onAppeared)
    {
        var controller = findControllerByType(controllerType);

        return pushController(controller, onAppear, onAppeared);
    }

    public static void PushController(UIViewController controller)
    {
        if (navigationController != null)
        {
            navigationController.pushController(controller, null, null);
        }
    }

    public static void PushController(UIViewController controller, ControllerDelegate onAppear, ControllerDelegate onAppeared)
    {
        if (navigationController != null)
        {
            navigationController.pushController(controller, onAppear, onAppeared);
        }
    }

    public static void PushController(System.Type controllerType)
    {
        if (navigationController != null)
        {
            navigationController.pushController(controllerType, null, null);
        }
    }

    public static void PushController(System.Type controllerType, ControllerDelegate onAppear, ControllerDelegate onAppeared)
    {
        if (navigationController != null)
        {
            navigationController.pushController(controllerType, null, null);
        }
    }

    public void pushController(string controllerPath, ControllerDelegate onAppear, ControllerDelegate onAppeared)
    {
        string[] paths = controllerPath.Split('?');
        var path = GetControllerPathUrl(controllerPath);

        if (paths.Length > 0)
        {
            if (mapControllerPath.ContainsKey(path))
            {
                var view = mapControllerPath[path];
                if (view != null)
                {
                    view.controllerParameters = GetControllerPathParams(controllerPath);
                    var navData = pushController(view, onAppear, onAppeared);
                    if (navData != null)
                    {
                        navData.parameter = controllerPath;
                    }
                }
            }
        }
    }

    public static void PushController(string controllerPath, ControllerDelegate onAppear, ControllerDelegate onAppeared)
    {
        if (navigationController != null)
        {
            navigationController.pushController(controllerPath, onAppear, onAppeared);
        }
    }

    public static void PushController(string controllerPath)
    {
        if (navigationController != null)
        {
            PushController(controllerPath, null, null);
        }
    }

    public static void PushControllerAsFirst(UIViewController controller)
    {
        if (navigationController != null)
        {
            navigationController.pushController(controller, null, null);
        }
    }

    public static void PushControllerAsFirst(UIViewController controller, ControllerDelegate onAppear, ControllerDelegate onAppeared)
    {
        if (navigationController != null)
        {
            navigationController.pushController(controller, onAppear, onAppeared);
        }
    }

    public static void PushControllerAsFirst(System.Type controllerType)
    {
        if (navigationController != null)
        {
            navigationController.pushControllerAsFirst(controllerType, null, null);
        }
    }

    public void dismissController(ControllerDelegate onDisappear, ControllerDelegate onDisappeared)
    {
        if (controllerStacks.Count == 0)
        {
            Debug.LogWarning("Controller Stacks is 0");
            return;
        }
        
        var current = controllerStacks.Count > 0 ? controllerStacks.Pop() : null;
        var previous = controllerStacks.Count > 0 ? controllerStacks.Peek() : null;

        AnimationClip showAnimClip = null, hideAnimClip = null;
        if (current != null)
        {
            GetNavigationAnimation(current.viewController, false, out showAnimClip, out hideAnimClip);
        }

        if (current != null && previous != null)
        {
            Animation currentTarget = current.viewController.GetComponent<Animation>();
            if (currentTarget != null && hideAnimClip != null)
            {
                var anim = ActiveAnimation.Play(currentTarget, hideAnimClip.name, AnimationOrTween.Direction.Forward, AnimationOrTween.EnableCondition.DoNothing, AnimationOrTween.DisableCondition.DisableAfterForward);

                if (onDisappear != null)
                {
                    onDisappear(current.viewController);
                }

                current.viewController.OnDissapear();

                anim.onFinished = (a) =>
                {
                    if (onDisappeared != null)
                    {
                        onDisappeared(current.viewController);
                    }
                    current.viewController.OnDisappeared();
                };
            }
        }

        if (previous != null)
        {
            Animation oldTarget = previous.viewController.GetComponent<Animation>();
            if (oldTarget != null && showAnimClip != null)
            {
                var anim = ActiveAnimation.Play(oldTarget, showAnimClip.name, AnimationOrTween.Direction.Forward, AnimationOrTween.EnableCondition.EnableThenPlay, AnimationOrTween.DisableCondition.DoNotDisable);

                if (string.IsNullOrEmpty(current.parameter) == false)
                {
                    var param = GetControllerPathParams(previous.parameter);
                    previous.viewController.controllerParameters = param;//.OnControllerParams(param);
                }

                previous.viewController.OnAppear();

                anim.onFinished = (a) =>
                    {
                        previous.viewController.OnAppeared();
                    };
            }

            currentController = previous.viewController;
            
        }
        PrintControllerStacks();
    }

    public void dismissToFirstController(ControllerDelegate onDisappear, ControllerDelegate onDisappeared)
    {
        if (controllerStacks.Count == 0)
        {
            Debug.LogWarning("Controller Stacks is 0");
            return;
        }
        
        var current = controllerStacks.Count > 0 ? controllerStacks.Pop() : null;

        AnimationClip showAnimClip = null, hideAnimClip = null;
        if (current != null)
        {
            GetNavigationAnimation(current.viewController, false, out showAnimClip, out hideAnimClip);
        }

        if (current != null)
        {
            Animation currentTarget = current.viewController.GetComponent<Animation>();

            if (currentTarget != null && hideAnimClip != null)
            {
                var anim = ActiveAnimation.Play(currentTarget, hideAnimClip.name, AnimationOrTween.Direction.Forward, AnimationOrTween.EnableCondition.DoNothing, AnimationOrTween.DisableCondition.DisableAfterForward);

                if (onDisappear != null)
                {
                    onDisappear(current.viewController);
                }
                if (onDisappeared != null && anim != null)
                {
                    anim.onFinished = (a) =>
                    {
                        onDisappeared(current.viewController);
                    };
                }
            }
        }

        if (firstActiveController != null)
        {
            controllerStacks.Clear();
            foreach (var firstController in firstActiveController)
            {
                PushToStack(firstController, "");
                Animation oldTarget = firstController.GetComponent<Animation>();
                if (oldTarget != null && showAnimClip != null)
                {
                    ActiveAnimation.Play(oldTarget, showAnimClip.name, AnimationOrTween.Direction.Forward, AnimationOrTween.EnableCondition.EnableThenPlay, AnimationOrTween.DisableCondition.DoNotDisable);
                }

                currentController = firstController;
            }
        }
        PrintControllerStacks();
    }

    public void dismissToFirstController()
    {
        dismissToFirstController(null, null);
    }

    public static void DismissToFirstController(ControllerDelegate onDisappear, ControllerDelegate onDisappeared)
    {
        if (navigationController != null)
        {
            navigationController.dismissToFirstController(onDisappear, onDisappeared);
        }
    }

    public static void DismissToFirstController()
    {
        if (navigationController != null)
        {
            navigationController.dismissToFirstController();
        }
    }

    public void dismissController()
    {
        dismissController(null, null);
    }

    public static void DismissController()
    {
        if (navigationController != null)
        {
            navigationController.dismissController(null, null);
        }
    }

    public static void DismissController(ControllerDelegate onDisappear, ControllerDelegate onDisappeared)
    {
        if (navigationController != null)
        {
            navigationController.dismissController(onDisappear, onDisappeared);
        }
    }

    public void registerViewController(UIViewController controller)
    {
        if (viewControllers.Contains(controller) == false)
        {
            viewControllers.Add(controller);
        }
    }

    public static void RegisterViewController(UIViewController controller)
    {
        if (navigationController != null)
        {
            navigationController.registerViewController(controller);
        }
    }

    public static void Broadcast(string method, object value)
    {
        if (navigationController != null)
        {
            foreach (var controller in navigationController.viewControllers)
            {
                controller.SendMessage(method, value, SendMessageOptions.DontRequireReceiver);
            }
        }
    }

    #endregion


    #region Internal Methods

    private NavigationData PushToStack(UIViewController controller, string param)
    {
        NavigationData data = new NavigationData(controller, param);
        if (controller.StackPushable)
        {
            controllerStacks.Push(data);
        }

        return data;
    }

    private void GetNavigationAnimation(UIViewController controller, bool isPush, out AnimationClip showAnimClip, out AnimationClip hideAnimClip)
    {
        if (isPush)
        {
            showAnimClip = controller.showForwardAnimation != null ? controller.showForwardAnimation : showForwardAnimation;
            hideAnimClip = controller.hideForwardAnimation != null ? controller.hideForwardAnimation : hideForwardAnimation;
        }
        else
        {
            showAnimClip = controller.showBackAnimation != null ? controller.showBackAnimation : showBackAnimation;
            hideAnimClip = controller.hideBackAnimation != null ? controller.hideBackAnimation : hideBackAnimation;
        }
    }

    private UIViewController findControllerByType(System.Type controllerType)
    {
        foreach (UIViewController controller in viewControllers)
        {
            if (controller == null)
            {
                continue;
            }

            if (controller.GetType() == controllerType)
            {
                return controller;
            }
        }

        return null;
    }

    #endregion
}
