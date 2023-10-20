import * as ActionTypes from "../actionTypes";

const initialState = {
  isLoading: false,
  errMsg: null,
  successMsg: "",
  data: [],
  companies: "",
  company_data: [],
  company_industry: [],
  company_profile: {},
  company_job: [],
  company_email_data: {},
  send_email: "",
  company_timeline: [],
  company_timeline_comments: "",
  send_company_link: "",
};

export default (state = initialState, action) => {
  switch (action.type) {
    case ActionTypes.GET_COMPANIES_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        data: action.payload,
      };

    case ActionTypes.GET_COMPANIES_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        data: [],
      };

    case ActionTypes.FETCH_COMPANY_DATA_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        company_data: action.payload,
      };

    case ActionTypes.FETCH_COMPANY_DATA_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        company_data: [],
      };

    case ActionTypes.FETCH_COMPANY_DATA_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        company_data: [],
      };

    case ActionTypes.GET_COMPANIES_JOBS_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        data: action.payload,
      };

    case ActionTypes.GET_COMPANIES_JOBS_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        data: [],
      };

    case ActionTypes.GET_COMPANIES_JOBS_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload.message,
        successMsg: "",
        data: [],
      };

    case ActionTypes.GET_COMPANIES_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload.message,
        successMsg: "",
        data: [],
      };

    case ActionTypes.GET_COMPANIES_INDUSTRY_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        company_industry: action.payload,
      };

    case ActionTypes.GET_COMPANIES_INDUSTRY_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        company_industry: [],
      };

    case ActionTypes.GET_COMPANIES_INDUSTRY_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload.message,
        successMsg: "",
        company_industry: [],
      };

    case ActionTypes.GET_COMPANY_BY_ID_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        company_profile: action.payload,
      };

    case ActionTypes.GET_COMPANY_BY_ID_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        company_profile: {},
      };

    case ActionTypes.GET_COMPANY_BY_ID_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload.message,
        successMsg: "",
        company_profile: {},
      };

    case ActionTypes.GET_COMPANY_RELEVANT_JOBS_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        company_job: action.payload,
      };

    case ActionTypes.GET_COMPANY_RELEVANT_JOBS_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        company_job: [],
      };

    case ActionTypes.GET_COMPANY_RELEVANT_JOBS_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload.message,
        successMsg: "",
        company_job: [],
      };

    case ActionTypes.GET_COMPANY_EMAIL_TEMPLATE_DATA_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        company_email_data: action.payload,
      };

    case ActionTypes.GET_COMPANY_EMAIL_TEMPLATE_DATA_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        company_email_data: {},
      };

    case ActionTypes.GET_COMPANY_EMAIL_TEMPLATE_DATA_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload.message,
        successMsg: "",
        company_email_data: {},
      };

    case ActionTypes.GET_COMPANY_TIMELINE_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        company_timeline: action.payload,
      };

    case ActionTypes.GET_COMPANY_TIMELINE_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        company_timeline: [],
      };

    case ActionTypes.GET_COMPANY_TIMELINE_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload.message,
        successMsg: "",
        company_timeline: [],
      };

    case ActionTypes.POST_COMPANIES_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        companies: action.payload,
      };

    case ActionTypes.POST_COMPANIES_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        companies: "",
      };

    case ActionTypes.POST_COMPANIES_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload.message,
        successMsg: "",
        companies: action.payload,
      };

    case ActionTypes.POST_COMPANY_TIMELINE_COMMENTS_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        company_timeline_comments: action.payload,
      };

    case ActionTypes.POST_COMPANY_TIMELINE_COMMENTS_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        company_timeline_comments: "",
      };

    case ActionTypes.POST_COMPANY_TIMELINE_COMMENTS_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload.message,
        successMsg: "",
        company_timeline_comments: "",
      };

    case ActionTypes.UPDATE_COMPANY_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        companies: action.payload,
      };

    case ActionTypes.UPDATE_COMPANY_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        companies: "",
      };

    case ActionTypes.UPDATE_COMPANY_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload.message,
        successMsg: "",
        companies: "",
      };

    case ActionTypes.DELETE_COMPANIES_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        companies: action.payload,
      };

    case ActionTypes.DELETE_COMPANIES_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        companies: "",
      };

    case ActionTypes.DELETE_COMPANIES_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload.message,
        successMsg: "",
        companies: "",
      };

    case ActionTypes.Add_NEW_COMPANY_CONTACT_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
      };

    case ActionTypes.Add_NEW_COMPANY_CONTACT_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
      };

    case ActionTypes.Add_NEW_COMPANY_CONTACT_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload.message,
        successMsg: "",
      };

    default:
      return state;
  }
};
