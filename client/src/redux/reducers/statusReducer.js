import * as ActionTypes from "../actionTypes";

const initialState = {
  isLoading: false,
  errMsg: null,
  status: [],
};

export default (state = initialState, action) => {
  switch (action.type) {
    case ActionTypes.GET_STATUS:
      return { status: action.payload };

    default:
      return state;
  }
};
