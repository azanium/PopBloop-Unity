using UnityEngine;
using System.Collections;
using System;

using PB.Common;
using PB.Client;
using PB.Game;

[RequireComponent(typeof(CharacterController))]
public class PBThirdPersonControllerOriginal : MonoBehaviour 
{
	#region MemVars & Props
    
    public static event Action OnPlayerMoved = null;
    public static event Action<ControllerColliderHit> OnPlayerCollided = null;

    public enum CharacterState
    {
        Idle = 0,
        Walking = 1,
        Trotting = 2,
        Running = 3,
        Jumping = 4,
        MoveToInteract = 5,
        Interacting = 6
    }

    private CharacterState _characterState = CharacterState.Idle;

    public float walkMaxAnimationSpeed = 0.75f;
    public float trotMaxAnimationSpeed = 1.0f;
    public float runMaxAnimationSpeed = 1.0f;
    public float jumpAnimationSpeed = 1.15f;
    public float landAnimationSpeed = 1.0f;

    private CharacterController controller;
		
	// The speed when walking
	public float walkSpeed = 1.2f;
	// after trotAfterSeconds of walking we trot with trotSpeed
	public float trotSpeed = 4.0f;
	// when pressing "Fire3" button (cmd) we start running
	public float runSpeed = 6.0f;
	
	// The speed of interpolation when changing walk direction (front to back, etc)
	public float turnRotateSpeed = 10f;
	
	// Is the direction we walk take account of camera direction
	public bool walkUseCameraDirection = false;
	
	public float inAirControlAcceleration = 3.0f;
	
	// How high do we jump when pressing jump and letting go immediately
	public float jumpHeight = 0.5f;
	// We add extraJumpHeight meters on top when holding the button down longer while jumping
	public float extraJumpHeight = 2.5f;
	
	// The gravity for the character
	public float gravity = 20.0f;
	// The gravity in controlled descent mode
	public float controlledDescentGravity = 2.0f;
	public float speedSmoothing = 10.0f;
	public float rotateSpeed = 500.0f;
	public float trotAfterSeconds = 3.0f;
	
	public bool canJump = true;
	public bool canControlDescent = false;
	public bool canWallJump = false;
	
	private float jumpRepeatTime = 0.05f;
	private float wallJumpTimeout = 0.15f;
	private float jumpTimeout = 0.15f;
	private float groundedTimeout = 0.25f;
	
	// The camera doesnt start following the target immediately but waits for a split second to avoid too much waving around.
	private float lockCameraTimer = 0.0f;

    public Vector3 MoveDirection
    {
        get { return moveDirection; }
        set { moveDirection = value; }
    }
	// The current move direction in x-z
	private Vector3 moveDirection = Vector3.zero;
	// The current vertical speed
	private float verticalSpeed = 0.0f;
	// The current x-z move speed
	private float moveSpeed = 0.0f;
	
	// The last collision flags returned from controller.Move
	private CollisionFlags collisionFlags; 
	
	// Are we jumping? (Initiated with jump button and not grounded yet)
	private bool jumping = false;
	private bool jumpingReachedApex = false;
	
	// Are we moving backwards (This locks the camera to not do a 180 degree spin)
	private bool movingBack = false;
	// Is the user pressing any keys?
	private bool isMoving = false;
	// When did the user start walking (Used for going into trot after a while)
	//private float walkTimeStart = 0.0f;
	// Last time the jump button was clicked down
	private float lastJumpButtonTime = -10.0f;
	// Last time we performed a jump
	private float lastJumpTime = -1.0f;
	// Average normal of the last touched geometry
	private Vector3 wallJumpContactNormal;
	private float wallJumpContactNormalHeight;
	
	// the height we jumped from (Used to determine for how long to apply extra jump power after jumping.)
	private float lastJumpStartHeight = 0.0f;
	// When did we touch the wall the first time during this jump (Used for wall jumping)
	private float touchWallJumpTime = -1.0f;
	
	private Vector3 inAirVelocity = Vector3.zero;
	
	private float lastGroundedTime = 0.0f;
	
	//private float lean = 0.0f;
	private bool slammed = false;
	
	private bool isControllable = true;

    protected enum MotionType
    {
        WASD = 0,
        Drag
    };
    protected MotionType _motionType = MotionType.WASD;

    private bool _isSwimming = false;

	#endregion
	
	
	#region MonoBehavior Methods
	
	void Awake() 
    {
		moveDirection = transform.TransformDirection(Vector3.forward);	
	}

    void Start()
    {
        //targetDragPosition = transform.position;
        _motionType = MotionType.WASD;
    }
	
	void OnControllerColliderHit(ControllerColliderHit hit)
	{
	    Debug.DrawRay(hit.point, hit.normal);
		if (hit.moveDirection.y > 0.01) 
			return;
		wallJumpContactNormal = hit.normal;

        if (hit.normal == Vector3.up && hit.collider.tag == LevelConstants.TagWater)
        {
            _isSwimming = true;

            SendMessage("PlayerStartSwimming", collider.transform.position, SendMessageOptions.DontRequireReceiver);
        }
        else
        {
            if (_isSwimming)
            {
                _isSwimming = false;

                SendMessage("PlayerEndSwimming", collider.transform.position, SendMessageOptions.DontRequireReceiver);
            }
        }
	}
	

	void Update() 
    {
		if (!isControllable)
		{
			// kill all inputs if not controllable.
			Input.ResetInputAxes();
		}
	
		if (InputManager.GetButtonDown ("Jump"))
		{
			lastJumpButtonTime = Time.time;
		}

        var v = Input.GetAxisRaw("Vertical");
        var h = Input.GetAxisRaw("Horizontal");

        bool moving = Mathf.Abs(h) > 0.1f || Mathf.Abs(v) > 0.1f;

        if (!moving)
        {
            UpdateDragMovement();
        }
        else
        {
            if (_motionType == MotionType.Drag)
            {
                StopDragMove();
            }
            UpdateSmoothedMovementDirection();
        }
		
		// Apply gravity
		// - extra power jump modifies gravity
		// - controlledDescent mode modifies gravity
		ApplyGravity ();
	
		// Perform a wall jump logic
		// - Make sure we are jumping against wall etc.
		// - Then apply jump in the right direction)
		if (canWallJump)
			ApplyWallJump();

        if (!IsSwimming)
        {
            // Apply jumping logic
            ApplyJumping();
        }

		// Calculate actual motion
        var movement = moveDirection * moveSpeed + new Vector3(0, verticalSpeed, 0) + inAirVelocity;
		movement *= Time.deltaTime;


		// Move the controller
		controller = GetComponent<CharacterController>();
		wallJumpContactNormal = Vector3.zero;
		
		if (PBConstants.IsHoverShoutUI == false)
		{
            collisionFlags = controller.Move(movement);

            if (_motionType == MotionType.Drag && (collisionFlags & CollisionFlags.CollidedSides) != 0)
            {
                StopDragMove();
            }
		}
	

		// Set rotation to the move direction
		if (IsGrounded())
		{
			if(slammed) // we got knocked over by an enemy. We need to reset some stuff
			{
				slammed = false;
				controller.height = 2f;
				transform.position = new Vector3(transform.position.x, 0.75f, transform.position.z);
			}
			
		    if (InputManager.IsKeyClearForGameControl)
			{
                transform.rotation = Quaternion.Slerp(transform.rotation, Quaternion.LookRotation(moveDirection), Time.deltaTime * turnRotateSpeed);
			}
		}	
		else
		{
			if(!slammed)
			{
				var xzMove = movement;
				xzMove.y = 0f;
				if (xzMove.sqrMagnitude > 0.001f)
				{
					transform.rotation = Quaternion.LookRotation(xzMove);
				}
			}
		}	
		
		// We are in jump mode but just became grounded
		if (IsGrounded())
		{
			lastGroundedTime = Time.time;
			inAirVelocity = Vector3.zero;
			if (jumping)
			{
				jumping = false;
				SendMessage("DidLand", SendMessageOptions.DontRequireReceiver);
			}
		}

        HasStopped = !IsMoving();
	}
	
	#endregion
	
	
	#region ThirdPersonController Methods

    private Vector3 targetDragPosition;

    public void StartDragMove(Vector3 targetPosition)
    {
        targetDragPosition = targetPosition;
        _motionType = MotionType.Drag;

        Messenger<Vector3>.Broadcast(Messages.PLAYER_BEGINMOVE, targetPosition, MessengerMode.DONT_REQUIRE_LISTENER);
    }

    public void StopDragMove()
    {
        if (_motionType == MotionType.Drag)
        {
            if (OnPlayerMoved != null)
            {
                OnPlayerMoved();
            }
        }

        _characterState = CharacterState.Idle;
        _motionType = MotionType.WASD;
        moveSpeed = 0;
    }

    private void UpdateDragMovement()
    {
        var grounded = IsGrounded();

        Vector3 targetDirection = Vector3.zero;

        if (_motionType == MotionType.Drag)
        {
            if (SAHelper.CalcDistance(transform.position, targetDragPosition) >= 0.2f)
            {
                targetDirection = new Vector3(targetDragPosition.x - transform.position.x, 0, targetDragPosition.z - transform.position.z);
            }
            else
            {
                StopDragMove();
            }
        }

        // Grounded controls
        if (grounded)
        {
            // We store speed and direction seperately,
            // so that when the character stands still we still have a valid forward direction
            // moveDirection is always normalized, and we only update it if there is user input.
            if (targetDirection != Vector3.zero)
            {
                // If we are really slow, just snap to the target direction
                if (moveSpeed < walkSpeed * 0.9 && grounded)
                {
                    moveDirection = targetDirection.normalized;
                }
                // Otherwise smoothly turn towards it
                else
                {
                    moveDirection = Vector3.Slerp(moveDirection, targetDirection, rotateSpeed * Time.deltaTime);//Vector3.RotateTowards(moveDirection, targetDirection, rotateSpeed * Mathf.Deg2Rad * Time.deltaTime, 1000);

                    moveDirection = moveDirection.normalized;
                }
            }

            // Smooth the speed based on the current target direction
            var curSmooth = speedSmoothing * Time.deltaTime;

            // Choose target speed
            //* We want to support analog input but make sure you cant walk faster diagonally than just forward or sideways
            var targetSpeed = Mathf.Min(targetDirection.magnitude, 1.0f);

            _characterState = CharacterState.Idle;

            if (_motionType == MotionType.Drag)
            {
                targetSpeed *= walkSpeed;
                _characterState = CharacterState.Walking;
            }
             
            // Pick speed modifier
            if (Input.GetKey(KeyCode.LeftShift) || Input.GetKey(KeyCode.RightShift))
            {
                targetSpeed *= walkSpeed;
                _characterState = CharacterState.Walking;
            }
            else
            {
                targetSpeed *= runSpeed;
                _characterState = CharacterState.Running;
            }

            moveSpeed = Mathf.Lerp(moveSpeed, targetSpeed, curSmooth);

            // Reset walk time start when we slow down
            //if (moveSpeed < walkSpeed * 0.3f)
              //  walkTimeStart = Time.time;
        }
    }

	private void UpdateSmoothedMovementDirection()
	{
		var cameraTransform = Camera.main.transform;
		var grounded = IsGrounded();
		
		// Forward vector relative to the camera along the x-z plane	
		var forward = cameraTransform.TransformDirection(Vector3.forward);
		if (walkUseCameraDirection)
		{
			forward = cameraTransform.TransformDirection(forward);
		}
		
		forward.y = 0f;
		forward = forward.normalized;
	
		// Right vector relative to the camera
		// Always orthogonal to the forward vector
		var right = new Vector3(forward.z, 0f, -forward.x);
	
		var v = Input.GetAxisRaw("Vertical");
		var h = Input.GetAxisRaw("Horizontal");
	
		// Are we moving backwards or looking backwards
		if (v < -0.2f)
			movingBack = true;
		else
			movingBack = false;
		
		var wasMoving = isMoving;
		isMoving = Mathf.Abs (h) > 0.1f || Mathf.Abs (v) > 0.1f;

        // Target direction relative to the camera
        Vector3 targetDirection = h * right + v * forward;
		
		// Grounded controls
		if (grounded)
		{
			// Lock camera for short period when transitioning moving & standing still
			lockCameraTimer += Time.deltaTime;
			if (isMoving != wasMoving)
				lockCameraTimer = 0.0f;
	
			// We store speed and direction seperately,
			// so that when the character stands still we still have a valid forward direction
			// moveDirection is always normalized, and we only update it if there is user input.
			if (targetDirection != Vector3.zero)
			{
				// If we are really slow, just snap to the target direction
				if (moveSpeed < walkSpeed * 0.9 && grounded)
				{
					moveDirection = targetDirection.normalized;
				}
				// Otherwise smoothly turn towards it
				else
				{
                    moveDirection = Vector3.Slerp(moveDirection, targetDirection, rotateSpeed * Time.deltaTime);// Vector3.RotateTowards(moveDirection, targetDirection, rotateSpeed * Mathf.Deg2Rad * Time.deltaTime, 10000);
					
					moveDirection = moveDirection.normalized;
				}
			}
			
			// Smooth the speed based on the current target direction
			var curSmooth = speedSmoothing * Time.deltaTime;
			
			// Choose target speed
			//* We want to support analog input but make sure you cant walk faster diagonally than just forward or sideways
			var targetSpeed = Mathf.Min(targetDirection.magnitude, 1.0f);

            _characterState = CharacterState.Idle;

            if (_motionType == MotionType.Drag)
            {
                targetSpeed *= walkSpeed;
                _characterState = CharacterState.Walking;
            }

			// Pick speed modifier
			//if (InputManager.GetButton ("Fire3"))
            if (Input.GetKey(KeyCode.LeftShift) | Input.GetKey(KeyCode.RightShift))
			{
				targetSpeed *= walkSpeed;
                _characterState = CharacterState.Walking;
			}
			else
			{
				targetSpeed *= runSpeed;
                _characterState = CharacterState.Running;
			}
			
			moveSpeed = Mathf.Lerp(moveSpeed, targetSpeed, curSmooth);
			
			// Reset walk time start when we slow down 
			//if (moveSpeed < walkSpeed * 0.3f)
				//walkTimeStart = Time.time;
		}
		// In air controls
		else
		{
			// Lock camera while in air
			if (jumping)
				lockCameraTimer = 0.0f;

            if (isMoving)
            {
                inAirVelocity += targetDirection.normalized * Time.deltaTime * inAirControlAcceleration;
            }
		}
	}
	
	void ApplyWallJump ()
	{
		// We must actually jump against a wall for this to work
		if (!jumping)
			return;
	
		// Store when we first touched a wall during this jump
		if (collisionFlags == CollisionFlags.CollidedSides)
		{
			touchWallJumpTime = Time.time;
		}
	
		// The user can trigger a wall jump by hitting the button shortly before or shortly after hitting the wall the first time.
		var mayJump = lastJumpButtonTime > touchWallJumpTime - wallJumpTimeout && lastJumpButtonTime < touchWallJumpTime + wallJumpTimeout;
		if (!mayJump)
			return;
		
		// Prevent jumping too fast after each other
		if (lastJumpTime + jumpRepeatTime > Time.time)
			return;
		
			
		if (Mathf.Abs(wallJumpContactNormal.y) < 0.2f)
		{
			wallJumpContactNormal.y = 0f;
			moveDirection = wallJumpContactNormal.normalized;
			// Wall jump gives us at least trotspeed
			moveSpeed = Mathf.Clamp(moveSpeed * 1.5f, trotSpeed, runSpeed);
		}
		else
		{
			moveSpeed = 0f;
		}
		
		verticalSpeed = CalculateJumpVerticalSpeed (jumpHeight);
		DidJump();
		SendMessage("DidWallJump", null, SendMessageOptions.DontRequireReceiver);
	}
	
	void ApplyJumping ()
	{
		// Prevent jumping too fast after each other
		if (lastJumpTime + jumpRepeatTime > Time.time)
			return;
	
		if (IsGrounded()) {
			// Jump
			// - Only when pressing the button down
			// - With a timeout so you can press the button slightly before landing		
			if (canJump && Time.time < lastJumpButtonTime + jumpTimeout) {
				verticalSpeed = CalculateJumpVerticalSpeed (jumpHeight);
				SendMessage("DidJump", SendMessageOptions.DontRequireReceiver);
			}
		}
	}
	
	
	void ApplyGravity ()
	{
		if (isControllable)	// don't move player at all if not controllable.
		{
			// Apply gravity
			var jumpButton = InputManager.GetButton("Jump");
			
			// * When falling down we use controlledDescentGravity (only when holding down jump)
			var controlledDescent = canControlDescent && verticalSpeed <= 0.0 && jumpButton && jumping;
			
			// When we reach the apex of the jump we send out a message
			if (jumping && !jumpingReachedApex && verticalSpeed <= 0.0)
			{
				jumpingReachedApex = true;
				SendMessage("DidJumpReachApex", SendMessageOptions.DontRequireReceiver);
			}
		
			// * When jumping up we don't apply gravity for some time when the user is holding the jump button
			//   This gives more control over jump height by pressing the button longer
			var extraPowerJump = IsJumping() && verticalSpeed > 0.0 && jumpButton && transform.position.y < lastJumpStartHeight + extraJumpHeight;

            if (controlledDescent)
            {
                verticalSpeed -= controlledDescentGravity * Time.deltaTime;
            }
            else if (extraPowerJump)
            {
                return;
            }
            else if (IsGrounded())
            {
                //if (!IsSwimming)
                {
                    verticalSpeed = 1.0f * Time.deltaTime;
                }
            }
            else
            {
                verticalSpeed -= gravity * Time.deltaTime;
            }
		}
	}
	
	float CalculateJumpVerticalSpeed (float targetJumpHeight)
	{
		// From the jump height and gravity we deduce the upwards speed 
		// for the character to reach at the apex.
		return Mathf.Sqrt(2 * targetJumpHeight * gravity);
	}
	
	void DidJump()
	{
		jumping = true;
		jumpingReachedApex = false;
		lastJumpTime = Time.time;
		lastJumpStartHeight = transform.position.y;
		touchWallJumpTime = -1;
		lastJumpButtonTime = -10;
	}

	public void HidePlayer()
	{
		GameObject.Find("rootJoint").GetComponent<SkinnedMeshRenderer>().enabled = false;
		isControllable = false;
	}
	
	public void ShowPlayer()
	{
		GameObject.Find("rootJoint").GetComponent<SkinnedMeshRenderer>().enabled = true;
		isControllable = true;
	}
	
	public float GetSpeed() 
    {
		return moveSpeed;
	}
	
	public bool IsJumping() 
    {
		return jumping;
	}
	
	public bool IsGrounded() 
    {
		return (collisionFlags & CollisionFlags.CollidedBelow) != 0;
	}

    public bool IsWalking()
    {
        return _characterState == CharacterState.Walking;
    }

    public bool IsRunning()
    {
        return _characterState == CharacterState.Running;
    }

    public bool IsThrotting()
    {
        return _characterState == CharacterState.Trotting;
    }
	
	public void SuperJump(float height)
	{
		verticalSpeed = CalculateJumpVerticalSpeed (height);
		collisionFlags = CollisionFlags.None;
		SendMessage("DidJump", SendMessageOptions.DontRequireReceiver);
	}
	
	public void SuperJump(float height, Vector3 jumpVelocity)
	{
		verticalSpeed = CalculateJumpVerticalSpeed (height);
		inAirVelocity = jumpVelocity;
		
		collisionFlags = CollisionFlags.None;
		SendMessage("DidJump", SendMessageOptions.DontRequireReceiver);
	}
	
	public void Slam(Vector3 direction)
	{
		verticalSpeed = CalculateJumpVerticalSpeed (1f);
		inAirVelocity = direction * 6f;
		direction.y = 0.6f;
		Quaternion.LookRotation(-direction);
		CharacterController controller = GetComponent<CharacterController>();
		controller.height = 0.5f;
		slammed = true;
		collisionFlags = CollisionFlags.None;
		SendMessage("DidJump", SendMessageOptions.DontRequireReceiver);
	}
	
	public Vector3 GetDirection() 
    {
		return moveDirection;
	}
	
	public bool IsMovingBackwards() 
    {
		return movingBack;
	}
	
	public float GetLockCameraTimer()
	{
		return lockCameraTimer;
	}

    private bool _hasStopped = true;
    public bool HasStopped
    {
        get { return _hasStopped; }
        set
        {
            if (_hasStopped != value)
            {
                _hasStopped = value;

                if (value)
                {
                    BroadcastMessage("PlayerHasStopped", SendMessageOptions.DontRequireReceiver);
                }
            }
        }
    }

	public bool IsMoving()
	{
        bool wasdMove = (Mathf.Abs(Input.GetAxisRaw("Vertical")) + Mathf.Abs(Input.GetAxisRaw("Horizontal")) > 0.5) && (PBConstants.IsHoverShoutUI == false);
        bool dragMove = (_motionType == MotionType.Drag && (_characterState == CharacterState.Walking || _characterState == CharacterState.Running || _characterState == CharacterState.Trotting));

        return wasdMove || dragMove;
	}
	
	public bool HasJumpReachedApex ()
	{
		return jumpingReachedApex;
	}
	
	public bool IsGroundedWithTimeout()
	{
		return lastGroundedTime + groundedTimeout > Time.time;
	}

    public float GetAnimationSpeed()
    {
        float speed = 1f;
        if (_characterState == CharacterState.Running)
        {
            speed = Mathf.Clamp(controller.velocity.magnitude, 0.0f, runMaxAnimationSpeed);

        }
        else if (_characterState == CharacterState.Trotting)
        {
            speed = Mathf.Clamp(controller.velocity.magnitude, 0.0f, trotMaxAnimationSpeed);
        }
        else if (_characterState == CharacterState.Walking)
        {
            speed = Mathf.Clamp(controller.velocity.magnitude, 0.0f, walkMaxAnimationSpeed);
        }

        return speed;
    }
	
	public bool IsControlledDescent ()
	{
		// * When falling down we use controlledDescentGravity (only when holding down jump)
		var jumpButton = InputManager.GetButton("Jump");
		return canControlDescent && verticalSpeed <= 0.0 && jumpButton && jumping;
	}
	
	public void Reset()
	{
		gameObject.tag = LevelConstants.TagPlayer;
	}

    public bool IsSwimming
    {
        get { return _isSwimming; }
    }

    public void OnTriggerEnter(Collider collider)
    {
        if (collider.tag == LevelConstants.TagWater)
        {
            _isSwimming = true;

            SendMessage("PlayerStartSwimming", collider.transform.position, SendMessageOptions.DontRequireReceiver);

            return;
        }

        if (_characterState == CharacterState.MoveToInteract && _activeInteractionObject.collider == collider)
        {
            _characterState = CharacterState.Interacting;
            return;
        }
    }

    public void OnTriggerExit(Collider collider)
    {
        if (collider.tag == LevelConstants.TagWater)
        {
            _isSwimming = false;

            SendMessage("PlayerEndSwimming", SendMessageOptions.DontRequireReceiver);
        }
    }
	
	#endregion


    #region Messenger's Messages

    private void OnEnable()
    {
        Messenger<Vector3>.AddListener(Messages.PLAYER_MOVETO, OnPlayerMustDragMove);
        Messenger.AddListener(Messages.PLAYER_STOPMOVE, OnPlayerMustStopDragMove);
        Messenger<GameObject, Transform>.AddListener(Messages.PLAYER_INTERACTO, OnPlayerInteractTo);
    }

    private void OnDisable()
    {
        Messenger<Vector3>.RemoveListener(Messages.PLAYER_MOVETO, OnPlayerMustDragMove);
        Messenger.RemoveListener(Messages.PLAYER_STOPMOVE, OnPlayerMustStopDragMove);
        Messenger<GameObject, Transform>.RemoveListener(Messages.PLAYER_INTERACTO, OnPlayerInteractTo);
    }


    private void OnPlayerMustDragMove(Vector3 position)
    {
        StartDragMove(position);
        EndInteraction();
    }

    private void OnPlayerMustStopDragMove()
    {
        StopDragMove();
    }

    private GameObject _activeInteractionObject = null;
    private Transform _activeInteractionTransform = null;
    private bool _activeInteractionIsTrigger = false;

    private void StartInteraction(GameObject obj, Transform trans)
    {
        Collider collider = obj.GetComponent<Collider>();

        if (collider != null)
        {
            _activeInteractionIsTrigger = collider.isTrigger;
            collider.isTrigger = true;
            _activeInteractionObject = obj;
            _activeInteractionTransform = trans;

            _characterState = CharacterState.MoveToInteract;
        }
    }

    private void EndInteraction()
    {
        if (_activeInteractionObject != null && _activeInteractionTransform != null)
        {
            Collider collider = _activeInteractionObject.GetComponent<Collider>();
            if (collider != null)
            {
                collider.isTrigger = _activeInteractionIsTrigger;
            }
        }
    }

    private void OnPlayerInteractTo(GameObject obj, Transform trans)
    {
        StartInteraction(obj, trans);
    }

    #endregion
}
