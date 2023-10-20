import * as ActionTypes from "../actionTypes";

const initialState = {
  isLoading: false,
  tags: {},
};

export default (state = initialState, action) => {
  switch (action.type) {
    case ActionTypes.GET_TAGS_SUCCESS:
      return { tags: action.payload };

    default:
      return state;
  }
};
