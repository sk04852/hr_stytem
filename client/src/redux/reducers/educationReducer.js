import * as ActionTypes from "../actionTypes";

const initialState = {
  isLoading: false,
  errMsg: null,
  successMsg: "",
  education: [],
};

export default (state = initialState, action) => {
  switch (action.type) {
    case ActionTypes.GET_EDUCATION_DEGREE:
      return { education: action.payload };

    default:
      return state;
  }
};
