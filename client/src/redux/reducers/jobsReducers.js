import * as ActionTypes from "../actionTypes";

const initialState = {
  isLoading: false,
  errMsg: null,
  success: false,
  success_message: "",
  jobs: "",
  jobs_data: [],
  job_by_id: {},
  best_match_candidate: [],
  timeline: [],
  jobs_applicants: [],
  data: { data: { data: [] } },
  active_jobs: []
};

export default (state = initialState, action) => {
  switch (action.type) {
    case ActionTypes.GET_JOBS_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        success_message: action.payload.message,
        jobs_data: action.payload,
      };

    case ActionTypes.GET_JOBS_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        success_message: "",
        jobs_data: [],
      };

    case ActionTypes.GET_JOBS_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        success_message: "",
        jobs_data: [],
      };

      case ActionTypes.GET_ALL_ACTIVE_JOBS_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        success_message: action.payload.message,
        active_jobs: action.payload,
      };

    case ActionTypes.GET_ALL_ACTIVE_JOBS_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        success_message: "",
        active_jobs: [],
      };

    case ActionTypes.GET_ALL_ACTIVE_JOBS_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        success_message: "",
        active_jobs: [],
      };

    case ActionTypes.GET_JOB_BY_ID_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        success_message: action.payload.message,
        job_by_id: action.payload,
      };

    case ActionTypes.GET_JOB_BY_ID_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        success_message: "",
        job_by_id: {},
      };

    case ActionTypes.GET_JOB_BY_ID_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        success_message: "",
        job_by_id: {},
      };

    case ActionTypes.GET_JOBS_APPLICANTS_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        success_message: action.payload.message,
        jobs_applicants: action.payload,
      };

    case ActionTypes.GET_JOBS_APPLICANTS_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        success_message: "",
        jobs_applicants: [],
      };

    case ActionTypes.GET_JOBS_APPLICANTS_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        success_message: "",
        jobs_applicants: [],
      };

    //   case ActionTypes.GET_EMAIL_VERIFICATION_TEMPLATES_LINK_SUCCESS:
    //   return {
    //     ...state,
    //     isLoading: false,
    //     errMsg: null,
    //     success_message: action.payload.message,
    //     jobs_applicants: action.payload,
    //   };

    // case ActionTypes.GET_EMAIL_VERIFICATION_TEMPLATES_LINK_PROCESSING:
    //   return {
    //     ...state,
    //     isLoading: true,
    //     errMsg: null,
    //     success_message: "",
    //     jobs_applicants: [],
    //   };

    // case ActionTypes.GET_EMAIL_VERIFICATION_TEMPLATES_LINK_FAILED:
    //   return {
    //     ...state,
    //     isLoading: false,
    //     errMsg: action.payload,
    //     success_message: "",
    //     jobs_applicants: [],
    //   };

    case ActionTypes.ADD_JOBS_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        success_message: action.payload.message,
        jobs: action.payload,
      };

    case ActionTypes.ADD_JOBS_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        success_message: "",
        jobs: "",
      };

    case ActionTypes.ADD_JOBS_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        success_message: "",
        jobs: "",
      };

    case ActionTypes.GET_JOBS_TIMELINE_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: "",
        success_message: action.payload.message,
        timeline: action.payload,
      };

    case ActionTypes.GET_JOBS_TIMELINE_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: "",
        success_message: "",
        timeline: [],
      };

    case ActionTypes.GET_JOBS_TIMELINE_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        success_message: "",
        timeline: [],
      };

    case ActionTypes.GET_JOBSCANDIDATES_SUCCESS:
      return { data: action.payload };

    case ActionTypes.UPDATE_JOBS_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        success: true,
        success_message: action.payload.message,
        jobs: action.payload,
      };

    case ActionTypes.UPDATE_JOBS_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        success: false,
        success_message: "",
      };

    case ActionTypes.UPDATE_JOBS_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        success: false,
        success_message: "",
      };

    case ActionTypes.DELETE_JOBS_SUCCESS:
      return { ...state, isLoading: false, errMsg: null, jobs: action.payload };

    case ActionTypes.DELETE_JOBS_PROCESSING:
      return { ...state, isLoading: true, errMsg: null, jobs: "" };

    case ActionTypes.DELETE_JOBS_FAILED:
      return { ...state, isLoading: false, errMsg: action.payload, jobs: "" };

    case ActionTypes.DELETE_JOB_SINGLE_VIDEO_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        success_message: action.payload.message,
        jobs: action.payload,
      };

    case ActionTypes.DELETE_JOB_SINGLE_VIDEO_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        success_message: "",
        jobs: "",
      };

    case ActionTypes.DELETE_JOB_SINGLE_VIDEO_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        success_message: "",
        jobs: "",
      };

    case ActionTypes.FETCH_BEST_MATCH_CANDIDATES_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        success_message: action.payload.message,
        best_match_candidate: action.payload,
      };

    case ActionTypes.FETCH_BEST_MATCH_CANDIDATES_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        success_message: "",
        best_match_candidate: [],
      };

    case ActionTypes.FETCH_BEST_MATCH_CANDIDATES_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        success_message: "",
        best_match_candidate: [],
      };

    case ActionTypes.DUPLICATE_JOB_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        success_message: action.payload.message,
      };

    case ActionTypes.DUPLICATE_JOB_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        success_message: "",
      };

    case ActionTypes.DUPLICATE_JOB_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        success_message: "",
      };

    default:
      return state;
  }
};
