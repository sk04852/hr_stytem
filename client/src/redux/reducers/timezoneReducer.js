import * as ActionTypes from "../actionTypes";

const initialState = {
  isLoading: false,
  errMsg: null,
  timezone: [],
};

export default (state = initialState, action) => {
  switch (action.type) {
    case ActionTypes.GET_TIMEZONE:
      return { timezone: action.payload };

    default:
      return state;
  }
};
