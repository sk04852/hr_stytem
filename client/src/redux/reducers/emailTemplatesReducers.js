import * as ActionTypes from "../actionTypes";

const initialState = {
  isLoading: false,
  errMsg: null,
  success_message: "",
  data: [],
  email_template: "",
  email_template_by_id: {},
  wildcards: [],
};

export default (state = initialState, action) => {
  switch (action.type) {
    case ActionTypes.GET_EMAIL_TEMPLATES_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        success_message: action.payload.message,
        data: action.payload,
      };

    case ActionTypes.GET_EMAIL_TEMPLATES_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        success_message: "",
      };

    case ActionTypes.GET_EMAIL_TEMPLATES_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        success_message: "",
      };

    case ActionTypes.GET_EMAIL_TEMPLATE_BY_ID_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        success_message: action.payload.message,
        email_template_by_id: action.payload,
      };

    case ActionTypes.GET_EMAIL_TEMPLATE_BY_ID_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        success_message: "",
        email_template_by_id: {},
      };

    case ActionTypes.GET_EMAIL_TEMPLATE_BY_ID_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        success_message: "",
        email_template_by_id: {},
      };

    case ActionTypes.ADD_EMAIL_TEMPLATES_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        success_message: action.payload.message,
        email_template: action.payload,
      };

    case ActionTypes.ADD_EMAIL_TEMPLATES_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        success_message: "",
        email_template: "",
      };

    case ActionTypes.ADD_EMAIL_TEMPLATES_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        success_message: "",
        email_template: "",
      };

    case ActionTypes.UPDATE_EMAIL_TEMPLATES_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        success_message: action.payload.message,
        email_template: action.payload,
      };

    case ActionTypes.UPDATE_EMAIL_TEMPLATES_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        success_message: "",
        email_template: "",
      };

    case ActionTypes.UPDATE_EMAIL_TEMPLATES_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        success_message: "",
        email_template: "",
      };

    case ActionTypes.DELETE_EMAIL_TEMPLATES_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        success_message: action.payload.message,
        email_template: action.payload,
      };

    case ActionTypes.DELETE_EMAIL_TEMPLATES_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        success_message: "",
        email_template: "",
      };

    case ActionTypes.DELETE_EMAIL_TEMPLATES_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        success_message: "",
        email_template: "",
      };

    //----------------------- WILDCARDS REDUCERS -----------------//
    case ActionTypes.GET_WILDCARDS_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        success_message: action.payload.message,
        wildcards: action.payload,
      };

    case ActionTypes.GET_WILDCARDS_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        success_message: "",
        wildcards: [],
      };

    case ActionTypes.GET_WILDCARDS_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        success_message: "",
        wildcards: [],
      };

    default:
      return state;
  }
};
