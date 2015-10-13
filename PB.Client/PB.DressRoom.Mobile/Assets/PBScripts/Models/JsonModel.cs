using UnityEngine;

using System.Collections;
using System.Collections.Generic;

public class JsonModel<T>
{
    #region MemVars & Props

    #endregion


    #region Internal Methods

    #endregion


    #region Public Methods

    public static T CreateObject(string json)
    {
        T obj = LitJson.JsonMapper.ToObject<T>(json);

        return obj;
    }

    #endregion
}
