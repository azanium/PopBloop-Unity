// --------------------------------------------------------------------------------------------------------------------
// <copyright file="ParameterCode.cs" company="Exit Games GmbH">
//   Copyright (c) Exit Games GmbH.  All rights reserved.
// </copyright>
// <summary>
//   This enumeration contains the values used for event parameter, operation request parameter and operation response parameter.
// </summary>
// --------------------------------------------------------------------------------------------------------------------

namespace PB.Common
{
    /// <summary>
    /// This enumeration contains the values used for event parameter, operation request parameter and operation response parameter.
    /// </summary>
    public enum ParameterCode : byte
    {
        /// <summary>
        ///  Client key parameter used to establish secure communication.
        /// </summary>
        ClientKey = 16,

        /// <summary>
        /// Server key parameter used to establish secure communication.
        /// </summary>
        ServerKey = 17,

        /// <summary>
        /// The event code.
        /// </summary>
        EventCode = 60, 

        /// <summary>
        /// The username.
        /// </summary>
        Username = 91, 

        /// <summary>
        /// The old position.
        /// </summary>
        OldPosition = 92, 

        /// <summary>
        /// The position.
        /// </summary>
        Position = 93, 

        /// <summary>
        /// The properties.
        /// </summary>
        Properties = 94, 

        /// <summary>
        /// The item id.
        /// </summary>
        ItemId = 95, 

        /// <summary>
        /// The item type.
        /// </summary>
        ItemType = 96, 

        /// <summary>
        /// The properties revision.
        /// </summary>
        PropertiesRevision = 97, 

        /// <summary>
        /// The custom event code.
        /// </summary>
        CustomEventCode = 98, 

        /// <summary>
        /// The event data.
        /// </summary>
        EventData = 99, 

        /// <summary>
        /// The top left corner.
        /// </summary>
        TopLeftCorner = 100, 

        /// <summary>
        /// The tile dimensions.
        /// </summary>
        TileDimensions = 101, 

        /// <summary>
        /// The bottom right corner.
        /// </summary>
        BottomRightCorner = 102, 

        /// <summary>
        /// The world name.
        /// </summary>
        WorldName = 103, 

        /// <summary>
        /// The view distance.
        /// </summary>
        ViewDistanceEnter = 104, 

        /// <summary>
        /// The properties set.
        /// </summary>
        PropertiesSet = 105, 

        /// <summary>
        /// The properties unset.
        /// </summary>
        PropertiesUnset = 106, 

        /// <summary>
        /// The event reliability.
        /// </summary>
        EventReliability = 107, 

        /// <summary>
        /// The event receiver.
        /// </summary>
        EventReceiver = 108, 

        /// <summary>
        /// The subscribe.
        /// </summary>
        Subscribe = 109, 

        /// <summary>
        /// The view distance exit.
        /// </summary>
        ViewDistanceExit = 110, 

        /// <summary>
        /// The interest area id.
        /// </summary>
        InterestAreaId = 111, 

        /// <summary>
        /// The counter receive interval.
        /// </summary>
        CounterReceiveInterval = 112, 

        /// <summary>
        /// The counter name.
        /// </summary>
        CounterName = 113, 

        /// <summary>
        /// The counter time stamps.
        /// </summary>
        CounterTimeStamps = 114, 

        /// <summary>
        /// The counter values.
        /// </summary>
        CounterValues = 115,
        
        /// <summary>
        /// The current rotation.
        /// </summary>
        Rotation = 116,

        /// <summary>
        /// The previous rotation.
        /// </summary>
        OldRotation = 118,

        /// <summary>
        /// Animation
        /// </summary>
        Animation = 119,

        /// <summary>
        /// The Animation Wrap
        /// </summary>
        AnimationWrap = 120,

        /// <summary>
        /// The Animation Speed
        /// </summary>
        AnimationSpeed = 121,

        /// <summary>
        /// Login Security Token
        /// </summary>
        SecurityToken = 122,

        /// <summary>
        /// The World's Level Informations
        /// </summary>
        WorldLevel = 123,

        /// <summary>
        /// The player's random token, each player is unique
        /// </summary>
        PlayerToken = 124,

        /// <summary>
        /// The player's user id
        /// </summary>
        PlayerId = 125,

        /// <summary>
        /// The player's username
        /// </summary>
        PlayerUsername = 126,

        /// <summary>
        /// Chat Group
        /// </summary>
        ChatGroup = 127,

        /// <summary>
        /// Chat Message
        /// </summary>
        ChatMessage = 128,

        /// <summary>
        /// Avatar Name
        /// </summary>
        AvatarName = 129,

        /// <summary>
        /// Private Room
        /// </summary>
        PrivateRoom = 130,

        /// <summary>
        /// Inventory Item Type
        /// </summary>
        InventoryItemCode = 131,

        /// <summary>
        /// Inventory Item Name
        /// </summary>
        InventoryItemName = 132,

        /// <summary>
        /// Inventory Item Count
        /// </summary>
        InventoryItemWeight = 133,

        /// <summary>
        /// Inventory Item Count
        /// </summary>
        InventoryItemDescription = 134,

        /// <summary>
        /// If inventory operation is adding item count and substracting it
        /// </summary>
        InventoryItemIsAdding = 135,

        /// <summary>
        /// Inventories Data
        /// </summary>
        Inventories = 136,

        /// <summary>
        /// Equipment Code
        /// </summary>
        EquipmentCode = 137,

        /// <summary>
        /// Equipment Count
        /// </summary>
        EquipmentCount = 138,

        /// <summary>
        /// Equipments
        /// </summary>
        Equipments = 139,

        /// <summary>
        /// Quest Journal Data
        /// </summary>
        QuestJournal = 140,

        /// <summary>
        /// The quest ID
        /// </summary>
        QuestID = 141,

        /// <summary>
        /// Quest is active
        /// </summary>
        QuestActive = 142,

        /// <summary>
        /// Set Private Level
        /// </summary>
        PrivateLevel = 150,

        /// <summary>
        /// Friend Operation Code
        /// </summary>
        FriendOpCode = 151,

        /// <summary>
        /// Animation Layer
        /// </summary>
        AnimationLayer = 152,

        /// <summary>
        /// Password
        /// </summary>
        Password = 153,

        /// <summary>
        /// Animation Action type
        /// - 0 : Play
        /// - 1 : Stop
        /// </summary>
        AnimationAction = 154,

        /// <summary>
        /// Current CCU
        /// </summary>
        CurrentCCU = 155,

        /// <summary>
        /// Max CCU
        /// </summary>
        MaxCCU = 156,

        /// <summary>
        /// Game Item Id
        /// </summary>
        GameItemId = 157
    }
}