import * as ActionTypes from "../actionTypes";

const initialState = {
  isLoading: false,
  errMsg: "",
  language: [],
  language_by_id: "",
  successMsg: "",
};

export default (state = initialState, action) => {
  switch (action.type) {
    case ActionTypes.GET_LANGUAGES_SUCCESS:
      return { ...state, language: action.payload };

    case ActionTypes.GET_CANDIDATE_LANGUAGE_BY_ID_SUCCESS:
      return { ...state, language_by_id: action.payload };

    case ActionTypes.ADD_CANDIDATE_LANGUAGE_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload,
      };

    case ActionTypes.ADD_CANDIDATE_LANGUAGE_PROCESSING:
      return { ...state, isLoading: true, errMsg: null, successMsg: "" };

    case ActionTypes.ADD_CANDIDATE_LANGUAGE_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
      };

    case ActionTypes.ADD_CANDIDATE_LANGUAGE_MANUALLY_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload,
      };

    case ActionTypes.ADD_CANDIDATE_LANGUAGE_MANUALLY_PROCESSING:
      return { ...state, isLoading: true, errMsg: null, successMsg: "" };

    case ActionTypes.ADD_CANDIDATE_LANGUAGE_MANUALLY_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
      };

    case ActionTypes.UPDATE_CANDIDATE_LANGUAGE_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload,
      };

    case ActionTypes.UPDATE_CANDIDATE_LANGUAGE_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
      };

    case ActionTypes.UPDATE_CANDIDATE_LANGUAGE_FAILED:
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
