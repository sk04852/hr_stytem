import * as ActionTypes from "../actionTypes";

const initialState = {
  isLoading: false,
  errMsg: null,
  successMsg: "",
  data: [],
};

export default (state = initialState, action) => {
  switch (action.type) {
    case ActionTypes.GET_ACTIONS_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        data: action.payload,
      };

    case ActionTypes.GET_ACTIONS_PROCESSING:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: "",
        data: [],
      };

    case ActionTypes.GET_ACTIONS_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        data: [],
      };

    default:
      return state;
  }
};
