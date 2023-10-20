import * as ActionTypes from "../actionTypes";

const initialState = {
  isLoading: false,
  errMsg: null,
  successMsg: "",
  timeline_comments: "",
};

export default (state = initialState, action) => {
  switch (action.type) {
    case ActionTypes.POST_TIMELINE_COMMENTS_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        timeline_comments: action.payload,
      };

    case ActionTypes.POST_TIMELINE_COMMENTS_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        timeline_comments: "",
      };

    case ActionTypes.POST_TIMELINE_COMMENTS_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        timeline_comments: "",
      };

    default:
      return state;
  }
};
