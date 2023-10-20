import * as ActionTypes from "../actionTypes";

const initialState = {
  isLoading: false,
  errMsg: null,
  successMsg: "",
  data: { data: { data: [] } },
  candidates_data: [],
  apply_for_job: "",
  candidate_email_data: {},
  additional_courses_data: [],
  additional_courses: {},
  agreements_data: [],
  agreements: "",
  candidate_profile: {},
  job_history: "",
  education: "",
  all_custom_list: [],
  custom_list: "",
  download_cv_pdf: {},
  best_match_jobs: [],
  timeline: [],
  candidate_files: [],
  candidate_file_action: "",
  candidate_applied_jobs: [],
  candidate_previous_phase_jobs: [],
};

export default (state = initialState, action) => {
  switch (action.type) {
    case ActionTypes.ADD_CANDIDATE_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload,
      };

    case ActionTypes.ADD_CANDIDATE_PROCESSING:
      return { ...state, isLoading: true, errMsg: null, successMsg: "" };

    case ActionTypes.ADD_CANDIDATE_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
      };

    case ActionTypes.ADD_CANDIDATE_PERSONAL_INFORMATION_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload,
      };

    case ActionTypes.ADD_CANDIDATE_PERSONAL_INFORMATION_PROCESSING:
      return { ...state, isLoading: true, errMsg: null, successMsg: "" };

    case ActionTypes.ADD_CANDIDATE_PERSONAL_INFORMATION_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
      };

    case ActionTypes.ADD_CANDIDATE_JOB_HISTORY_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload,
      };

    case ActionTypes.ADD_CANDIDATE_JOB_HISTORY_PROCESSING:
      return { ...state, isLoading: true, errMsg: null, successMsg: "" };

    case ActionTypes.ADD_CANDIDATE_JOB_HISTORY_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
      };

    case ActionTypes.ADD_CANDIDATE_EDUCATION_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload,
      };

    case ActionTypes.ADD_CANDIDATE_EDUCATION_PROCESSING:
      return { ...state, isLoading: true, errMsg: null, successMsg: "" };

    case ActionTypes.ADD_CANDIDATE_EDUCATION_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
      };

    case ActionTypes.GET_ADDITIONAL_COURSES_BY_ID_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        additional_courses_data: action.payload,
      };

    case ActionTypes.GET_ADDITIONAL_COURSES_BY_ID_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        additional_courses_data: [],
      };

    case ActionTypes.GET_ADDITIONAL_COURSES_BY_ID_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        additional_courses_data: [],
      };

    case ActionTypes.ADD_ADDITIONAL_COURSES_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        additional_courses_data: action.payload,
      };

    case ActionTypes.ADD_ADDITIONAL_COURSES_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        additional_courses: {},
      };

    case ActionTypes.ADD_ADDITIONAL_COURSES_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        additional_courses: {},
      };

    case ActionTypes.UPDATE_CANDIDATE_ADDITIONAL_COURSES_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        additional_courses: action.payload,
      };

    case ActionTypes.UPDATE_CANDIDATE_ADDITIONAL_COURSES_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        additional_courses: {},
      };

    case ActionTypes.UPDATE_CANDIDATE_ADDITIONAL_COURSES_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        additional_courses: {},
      };

    case ActionTypes.DELETE_CANDIDATE_ADDITIONAL_COURSES_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        additional_courses: action.payload,
      };

    case ActionTypes.DELETE_CANDIDATE_ADDITIONAL_COURSES_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        additional_courses: {},
      };

    case ActionTypes.DELETE_CANDIDATE_ADDITIONAL_COURSES_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        additional_courses: {},
      };

    case ActionTypes.ADD_CANDIDATE_ACTION_ID_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        apply_for_job: action.payload,
      };

    case ActionTypes.ADD_CANDIDATE_ACTION_ID_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        apply_for_job: "",
      };

    case ActionTypes.ADD_CANDIDATE_ACTION_ID_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        apply_for_job: "",
      };

    case ActionTypes.CANDIDATE_APPLY_TO_JOB_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        apply_for_job: action.payload,
      };

    case ActionTypes.CANDIDATE_APPLY_TO_JOB_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        apply_for_job: "",
      };

    case ActionTypes.CANDIDATE_APPLY_TO_JOB_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        apply_for_job: "",
      };

    case ActionTypes.REJECTED_CANDIDATE_ACTION_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        apply_for_job: action.payload,
      };

    case ActionTypes.REJECTED_CANDIDATE_ACTION_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        apply_for_job: "",
      };

    case ActionTypes.REJECTED_CANDIDATE_ACTION_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        apply_for_job: "",
      };

    case ActionTypes.UPDATE_CANDIDATE_EDUCATION_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload,
      };

    case ActionTypes.UPDATE_CANDIDATE_EDUCATION_PROCESSING:
      return { ...state, isLoading: true, errMsg: null, successMsg: "" };

    case ActionTypes.UPDATE_CANDIDATE_EDUCATION_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
      };

    case ActionTypes.UPDATE_CANDIDATE_JOB_HISTORY_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload,
      };

    case ActionTypes.UPDATE_CANDIDATE_JOB_HISTORY_PROCESSING:
      return { ...state, isLoading: true, errMsg: null, successMsg: "" };

    case ActionTypes.UPDATE_CANDIDATE_JOB_HISTORY_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
      };

    case ActionTypes.UPDATE_CANDIDATE_SKILLS_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload,
      };

    case ActionTypes.UPDATE_CANDIDATE_SKILLS_PROCESSING:
      return { ...state, isLoading: true, errMsg: null, successMsg: "" };

    case ActionTypes.UPDATE_CANDIDATE_SKILLS_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
      };

    case ActionTypes.GET_UPLOAD_FILES_BY_ID_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        candidate_files: action.payload,
      };

    case ActionTypes.GET_UPLOAD_FILES_BY_ID_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        candidate_files: [],
      };

    case ActionTypes.GET_UPLOAD_FILES_BY_ID_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        candidate_files: [],
      };

    case ActionTypes.DELETE_UPLOAD_FILES_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        candidate_files_action: action.payload,
      };

    case ActionTypes.DELETE_UPLOAD_FILES_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        candidate_files_action: "",
      };

    case ActionTypes.DELETE_UPLOAD_FILES_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        candidate_files_action: "",
      };

    case ActionTypes.GET_CANDIDATE_PROFILE_BY_ID_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        candidate_profile: action.payload,
      };

    case ActionTypes.GET_CANDIDATE_PROFILE_BY_ID_PROCESSING:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: "",
        candidate_profile: {},
      };

    case ActionTypes.GET_CANDIDATE_PROFILE_BY_ID_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        candidate_profile: {},
      };

    case ActionTypes.GET_CANDIDATE_APPLIED_JOBS_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        candidate_applied_jobs: action.payload,
      };

    case ActionTypes.GET_CANDIDATE_APPLIED_JOBS_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        candidate_applied_jobs: [],
      };

    case ActionTypes.GET_CANDIDATE_APPLIED_JOBS_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        candidate_applied_jobs: [],
      };

    case ActionTypes.GET_CANDIDATE_PREVIOUS_PHASE_JOBS_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        candidate_previous_phase_jobs: action.payload,
      };

    case ActionTypes.GET_CANDIDATE_PREVIOUS_PHASE_JOBS_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        candidate_previous_phase_jobs: [],
      };

    case ActionTypes.GET_CANDIDATE_PREVIOUS_PHASE_JOBS_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        candidate_previous_phase_jobs: [],
      };

    case ActionTypes.UPLOAD_FILES_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
      };

    case ActionTypes.UPLOAD_FILES_PROCESSING:
      return { ...state, isLoading: true, errMsg: null, successMsg: "" };

    case ActionTypes.UPLOAD_FILES_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
      };

    case ActionTypes.GET_CANDIDATE_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: "",
        successMsg: action.payload.message,
        candidates_data: action.payload,
      };

    case ActionTypes.GET_CANDIDATE_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: "",
        successMsg: "",
        candidates_data: [],
      };

    case ActionTypes.GET_CANDIDATE_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        candidates_data: [],
      };

    case ActionTypes.GET_ALL_CANDIDATES_NEW_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        candidates_data: action.payload,
      };

    case ActionTypes.GET_ALL_CANDIDATES_NEW_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        candidates_data: [],
      };

    case ActionTypes.GET_ALL_CANDIDATES_NEW_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        candidates_data: [],
      };

    case ActionTypes.GET_CANDIDATE_EDUCATION_SUCCESS:
      return { data: action.payload };

    case ActionTypes.GET_CANDIDATE_JOB_HISTORY_SUCCESS:
      return { data: action.payload };

    case ActionTypes.GET_CANDIDATE_SKILLS_SUCCESS:
      return { data: action.payload };

    case ActionTypes.DELETE_CANDIDATE_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload,
      };

    case ActionTypes.DELETE_CANDIDATE_PROCESSING:
      return { ...state, isLoading: true, errMsg: null, successMsg: "" };

    case ActionTypes.DELETE_CANDIDATE_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
      };

    case ActionTypes.DELETE_CANDIDATE_JOB_HISTORY_BY_ID_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        job_history: action.payload,
      };

    case ActionTypes.DELETE_CANDIDATE_JOB_HISTORY_BY_ID_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        job_history: "",
      };

    case ActionTypes.DELETE_CANDIDATE_JOB_HISTORY_BY_ID_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        job_history: "",
      };

    case ActionTypes.DELETE_CANDIDATE_EDUCATION_BY_ID_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        education: action.payload,
      };

    case ActionTypes.DELETE_CANDIDATE_EDUCATION_BY_ID_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        education: "",
      };

    case ActionTypes.DELETE_CANDIDATE_EDUCATION_BY_ID_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        education: "",
      };

    case ActionTypes.GET_CANDIDATE_AGREEMENT_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        agreements_data: action.payload,
      };

    case ActionTypes.GET_CANDIDATE_AGREEMENT_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        agreements_data: [],
      };

    case ActionTypes.GET_CANDIDATE_AGREEMENT_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        agreements_data: [],
      };

    case ActionTypes.CREATE_CANDIDATE_AGREEMENT_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        agreements: action.payload,
      };

    case ActionTypes.CREATE_CANDIDATE_AGREEMENT_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        agreements: "",
      };

    case ActionTypes.CREATE_CANDIDATE_AGREEMENT_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        agreements: "",
      };

    case ActionTypes.DELETE_CANDIDATE_AGREEMENT_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        agreements: action.payload,
      };

    case ActionTypes.DELETE_CANDIDATE_AGREEMENT_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        agreements: "",
      };

    case ActionTypes.DELETE_CANDIDATE_AGREEMENT_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        agreements: "",
      };

    case ActionTypes.GET_CANDIDATE_EMAIL_TEMPLATE_DATA_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        candidate_email_data: action.payload,
      };

    case ActionTypes.GET_CANDIDATE_EMAIL_TEMPLATE_DATA_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        candidate_email_data: "",
      };

    case ActionTypes.GET_CANDIDATE_EMAIL_TEMPLATE_DATA_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        candidate_email_data: "",
      };

    case ActionTypes.CREATE_CUSTOM_LIST_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        custom_list: action.payload,
      };

    case ActionTypes.CREATE_CUSTOM_LIST_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        custom_list: "",
      };

    case ActionTypes.CREATE_CUSTOM_LIST_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        custom_list: "",
      };

    case ActionTypes.CREATE_CUSTOM_LIST_MULTIPLE_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
      };

    case ActionTypes.CREATE_CUSTOM_LIST_MULTIPLE_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
      };

    case ActionTypes.CREATE_CUSTOM_LIST_MULTIPLE_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
      };

    case ActionTypes.REMOVE_CUSTOM_LIST_MULTIPLE_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
      };

    case ActionTypes.REMOVE_CUSTOM_LIST_MULTIPLE_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
      };

    case ActionTypes.REMOVE_CUSTOM_LIST_MULTIPLE_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
      };

    case ActionTypes.DELETE_SINGLE_CUSTOM_LIST_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        custom_list: action.payload,
      };

    case ActionTypes.DELETE_SINGLE_CUSTOM_LIST_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        custom_list: "",
      };

    case ActionTypes.DELETE_SINGLE_CUSTOM_LIST_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        custom_list: "",
      };

    case ActionTypes.UPDATE_CUSTOM_LIST_NAME_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        custom_list: action.payload,
      };

    case ActionTypes.UPDATE_CUSTOM_LIST_NAME_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        custom_list: "",
      };

    case ActionTypes.UPDATE_CUSTOM_LIST_NAME_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        custom_list: "",
      };

    case ActionTypes.UPDATE_CUSTOM_LIST_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        custom_list: action.payload,
      };

    case ActionTypes.UPDATE_CUSTOM_LIST_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        custom_list: "",
      };

    case ActionTypes.UPDATE_CUSTOM_LIST_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        custom_list: "",
      };

    case ActionTypes.DELETE_CUSTOM_LIST_NAME_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        custom_list: action.payload,
      };

    case ActionTypes.DELETE_CUSTOM_LIST_NAME_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        custom_list: "",
      };

    case ActionTypes.DELETE_CUSTOM_LIST_NAME_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        custom_list: "",
      };

    case ActionTypes.GET_CUSTOM_LIST_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        all_custom_list: action.payload,
      };

    case ActionTypes.GET_CUSTOM_LIST_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        all_custom_list: [],
      };

    case ActionTypes.GET_CUSTOM_LIST_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        all_custom_list: [],
      };

    case ActionTypes.DOWNLOAD_CANDIDATE_CV_PDF_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        download_cv_pdf: action.payload,
      };

    case ActionTypes.DOWNLOAD_CANDIDATE_CV_PDF_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        download_cv_pdf: {},
      };

    case ActionTypes.DOWNLOAD_CANDIDATE_CV_PDF_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        download_cv_pdf: {},
      };

    case ActionTypes.GET_CANDIDATE_TIMELINE_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        timeline: action.payload,
      };

    case ActionTypes.GET_CANDIDATE_TIMELINE_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        timeline: [],
      };

    case ActionTypes.GET_CANDIDATE_TIMELINE_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        timeline: [],
      };

    case ActionTypes.FETCH_BEST_MATCH_JOBS_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        best_match_jobs: action.payload,
      };

    case ActionTypes.FETCH_BEST_MATCH_JOBS_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        best_match_jobs: [],
      };

    case ActionTypes.FETCH_BEST_MATCH_JOBS_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        best_match_jobs: [],
      };

    default:
      return state;
  }
};
