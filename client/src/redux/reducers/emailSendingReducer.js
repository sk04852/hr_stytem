import * as ActionTypes from "../actionTypes";

const initialState = {
  isLoading: false,
  errMsg: null,
  successMsg: "",
  send_email: "",
};

export default (state = initialState, action) => {
  switch (action.type) {
    case ActionTypes.SEND_EMAIL_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        send_email: action.payload,
      };

    case ActionTypes.SEND_EMAIL_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        send_email: "",
      };

    case ActionTypes.SEND_EMAIL_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        send_email: "",
      };

      case ActionTypes.SEND_COMPANY_EMAIL_LINK_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message
      };

    case ActionTypes.SEND_COMPANY_EMAIL_LINK_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
      };

    case ActionTypes.SEND_COMPANY_EMAIL_LINK_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
      };

    default:
      return state;
  }
};
