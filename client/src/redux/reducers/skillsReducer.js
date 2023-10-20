import * as ActionTypes from "../actionTypes";

const initialState = {
  isLoading: false,
  errMsg: "",
  skills: {},
  successMsg: "",
};

export default (state = initialState, action) => {
  switch (action.type) {
    case ActionTypes.GET_SKILLS_SUCCESS:
      return { skills: action.payload };

    case ActionTypes.ADD_SKILLS_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload,
      };

    case ActionTypes.ADD_SKILLS_PROCESSING:
      return { ...state, isLoading: true, errMsg: null, successMsg: "" };

    case ActionTypes.ADD_SKILLS_FAILED:
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
