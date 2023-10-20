import * as ActionTypes from "../actionTypes";
import storage from "redux-persist/lib/storage";
import { persistReducer } from "redux-persist";
import { encryptTransform } from "redux-persist-transform-encrypt";

const initialState = {
  isLoading: false,
  errMsg: null,
  successMsg: "",
  data: [],
  isAuthenticated: false,
  auth_user: "",
  all_users: [],
  user_profile: "",
  users: "",
  single_user: {},
};

const UserReducer = (state = initialState, action) => {
  switch (action.type) {
    case ActionTypes.GET_USERS_SUCCESS:
      return { ...state, isLoading: false, data: action.payload };

    case ActionTypes.GET_USERS_PROCESSING:
      return { ...state, isLoading: true, data: "" };

    case ActionTypes.GET_USERS_FAILED:
      return { ...state, isLoading: false, errMsg: action.payload, data: "" };

    case ActionTypes.GET_SINGLE_USER_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        single_user: action.payload,
      };

    case ActionTypes.GET_SINGLE_USER_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        single_user: {},
      };

    case ActionTypes.GET_SINGLE_USER_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        single_user: {},
      };

    case ActionTypes.GET_USERS_PROFILE_SUCCESS:
      return { ...state, user_profile: action.payload };

    case ActionTypes.GET_ALL_USERS_SUCCESS:
      return { ...state, all_users: action.payload };

    case ActionTypes.LOGIN_USER_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        auth_user: action.payload,
        isAuthenticated: true,
      };

    case ActionTypes.LOGIN_USER_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        auth_user: "",
        isAuthenticated: false,
      };

    case ActionTypes.LOGIN_USER_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        auth_user: "",
        isAuthenticated: false,
      };

    case ActionTypes.LOGOUT_USER_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        auth_user: action.payload,
        user_profile: "",
        isAuthenticated: false,
      };

    case ActionTypes.LOGOUT_USER_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        auth_user: "",
        isAuthenticated: true,
      };

    case ActionTypes.LOGOUT_USER_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: action.payload.message,
        auth_user: "",
        isAuthenticated: true,
      };

    case ActionTypes.UPDATE_USERS_PROFILE_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload,
      };

    case ActionTypes.UPDATE_USERS_PROFILE_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
      };

    case ActionTypes.UPDATE_USERS_PROFILE_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
      };

    case ActionTypes.CHANGE_USER_PASSWORD_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
      };

    case ActionTypes.CHANGE_USER_PASSWORD_PROCESSING:
      return { ...state, isLoading: true, errMsg: null, successMsg: "" };

    case ActionTypes.CHANGE_USER_PASSWORD_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
      };

    case ActionTypes.ADD_NEW_USER_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        users: action.payload,
      };

    case ActionTypes.ADD_NEW_USER_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        users: "",
      };

    case ActionTypes.ADD_NEW_USER_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        users: "",
      };

    case ActionTypes.UPDATE_USER_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload,
      };

    case ActionTypes.UPDATE_USER_PROCESSING:
      return { ...state, isLoading: true, errMsg: null, successMsg: "" };

    case ActionTypes.UPDATE_USER_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
      };

    case ActionTypes.DELETE_USER_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        users: action.payload,
      };

    case ActionTypes.DELETE_USER_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        users: "",
      };

    case ActionTypes.DELETE_USER_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        users: "",
      };

    default:
      return state;
  }
};

const encryptor = encryptTransform({
  secretKey: "this-is-my-secret-key-2023",
});

const persistConfig = {
  key: "users",
  storage: storage,
  blacklist: ["isLoading"],
  transforms: [encryptor],
};

export default persistReducer(persistConfig, UserReducer);
