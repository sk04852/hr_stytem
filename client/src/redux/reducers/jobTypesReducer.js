import * as ActionTypes from "../actionTypes";

const initialState = {
  isLoading: false,
  errMsg: null,
  jobType: [],
};

export default (state = initialState, action) => {
  switch (action.type) {
    case ActionTypes.GET_JOB_TYPES:
      return { jobType: action.payload };

    default:
      return state;
  }
};
