import * as ActionTypes from "../actionTypes";

const initialState = {
  isLoading: false,
  errMsg: null,
  successMsg: "",
  data: [],
  role_permissions: [],
  user_permissions: [],
};

export default (state = initialState, action) => {
  switch (action.type) {
    case ActionTypes.GET_PERMISSIONS_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        data: action.payload,
      };

    case ActionTypes.GET_PERMISSIONS_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        data: [],
      };

    case ActionTypes.GET_PERMISSIONS_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        data: [],
      };

    case ActionTypes.ASSIGN_PERMISSIONS_TO_ROLE_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        role_permissions: action.payload,
      };

    case ActionTypes.ASSIGN_PERMISSIONS_TO_ROLE_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        role_permissions: [],
      };

    case ActionTypes.ASSIGN_PERMISSIONS_TO_ROLE_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        role_permissions: [],
      };

    case ActionTypes.ASSIGN_PERMISSIONS_TO_USER_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        user_permissions: action.payload,
      };

    case ActionTypes.ASSIGN_PERMISSIONS_TO_USER_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        user_permissions: [],
      };

    case ActionTypes.ASSIGN_PERMISSIONS_TO_USER_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        user_permissions: [],
      };

    default:
      return state;
  }
};
