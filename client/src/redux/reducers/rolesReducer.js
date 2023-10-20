import * as ActionTypes from "../actionTypes";

const initialState = {
  isLoading: false,
  errMsg: null,
  successMsg: "",
  roles: [],
  post_roles: "",
  user_roles: "",
  single_role: {},
};

export default (state = initialState, action) => {
  switch (action.type) {
    case ActionTypes.GET_ROLES_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        roles: action.payload,
      };

    case ActionTypes.GET_ROLES_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        roles: [],
      };

    case ActionTypes.GET_ROLES_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        roles: [],
      };

    case ActionTypes.GET_SINGLE_ROLE_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        single_role: action.payload,
      };

    case ActionTypes.GET_SINGLE_ROLE_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        single_role: {},
      };

    case ActionTypes.GET_SINGLE_ROLE_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        single_role: {},
      };

    case ActionTypes.POST_USER_ROLES_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        post_roles: action.payload,
      };

    case ActionTypes.POST_USER_ROLES_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        post_roles: "",
      };

    case ActionTypes.POST_USER_ROLES_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        post_roles: "",
      };

    case ActionTypes.UPDATE_USER_ROLES_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        user_roles: action.payload,
      };

    case ActionTypes.UPDATE_USER_ROLES_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        user_roles: "",
      };

    case ActionTypes.UPDATE_USER_ROLES_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        user_roles: "",
      };

    case ActionTypes.DELETE_USER_ROLES_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        user_roles: action.payload,
      };

    case ActionTypes.DELETE_USER_ROLES_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        user_roles: "",
      };

    case ActionTypes.DELETE_USER_ROLES_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        user_roles: "",
      };

    default:
      return state;
  }
};
