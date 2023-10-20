import * as ActionTypes from "../actionTypes";

const initialState = {
  isLoading: false,
  errMsg: null,
  successMsg: "",
  data: {},
};

export default (state = initialState, action) => {
  switch (action.type) {
    case ActionTypes.IMPORT_DATA_FROM_FILE_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        data: action.payload,
      };

    case ActionTypes.IMPORT_DATA_FROM_FILE_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        data: {},
      };

    case ActionTypes.IMPORT_DATA_FROM_FILE_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: "",
        data: {},
      };

    default:
      return state;
  }
};
