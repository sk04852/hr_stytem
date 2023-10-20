import * as ActionTypes from "../actionTypes";

const initialState = {
  isLoading: false,
  errMsg: null,
  success_message: "",
  events: [],
  calendars: [],
  share_calendar_users: [],
};

export default (state = initialState, action) => {
  switch (action.type) {
    case ActionTypes.GET_CALENDAR_EVENT_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        success_message: action.payload.message,
        events: action.payload,
      };

    case ActionTypes.GET_CALENDAR_EVENT_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        success_message: "",
        events: "",
      };

    case ActionTypes.GET_CALENDAR_EVENT_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        success_message: "",
        events: "",
      };

    case ActionTypes.CREATE_CALENDAR_EVENT_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        success_message: action.payload.message,
      };

    case ActionTypes.CREATE_CALENDAR_EVENT_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        success_message: "",
      };

    case ActionTypes.CREATE_CALENDAR_EVENT_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        success_message: "",
      };

    case ActionTypes.UPDATE_CALENDAR_EVENT_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        success_message: action.payload.message,
      };

    case ActionTypes.UPDATE_CALENDAR_EVENT_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        success_message: "",
      };

    case ActionTypes.UPDATE_CALENDAR_EVENT_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        success_message: "",
      };

    case ActionTypes.DELETE_CALENDAR_EVENT_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        success_message: action.payload.message,
      };

    case ActionTypes.DELETE_CALENDAR_EVENT_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        success_message: "",
      };

    case ActionTypes.DELETE_CALENDAR_EVENT_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        success_message: "",
      };

    case ActionTypes.GET_CALENDARS_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        success_message: action.payload.message,
        calendars: action.payload,
      };

    case ActionTypes.GET_CALENDARS_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        success_message: "",
        calendars: [],
      };

    case ActionTypes.GET_CALENDARS_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        success_message: "",
        calendars: [],
      };

    case ActionTypes.CREATE_NEW_CALENDAR_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        success_message: action.payload.message,
      };

    case ActionTypes.CREATE_NEW_CALENDAR_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        success_message: "",
      };

    case ActionTypes.CREATE_NEW_CALENDAR_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        success_message: "",
      };

    case ActionTypes.DELETE_CALENDAR_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        success_message: action.payload.message,
      };

    case ActionTypes.DELETE_CALENDAR_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        success_message: "",
      };

    case ActionTypes.DELETE_CALENDAR_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        success_message: "",
      };

    case ActionTypes.SHARE_CALENDAR_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        success_message: action.payload.message,
      };

    case ActionTypes.SHARE_CALENDAR_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        success_message: "",
      };

    case ActionTypes.SHARE_CALENDAR_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        success_message: "",
      };

    case ActionTypes.SHARE_CALENDAR_WITH_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        success_message: action.payload.message,
        share_calendar_users: action.payload,
      };

    case ActionTypes.SHARE_CALENDAR_WITH_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        success_message: "",
        share_calendar_users: [],
      };

    case ActionTypes.SHARE_CALENDAR_WITH_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        success_message: "",
        share_calendar_users: [],
      };

    case ActionTypes.DELETE_SHARE_CALENDAR_WITH_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        success_message: action.payload.message,
      };

    case ActionTypes.DELETE_SHARE_CALENDAR_WITH_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        success_message: "",
      };

    case ActionTypes.DELETE_SHARE_CALENDAR_WITH_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        success_message: "",
      };

    default:
      return state;
  }
};
