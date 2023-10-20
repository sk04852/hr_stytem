import * as ActionTypes from "../actionTypes";

const initialState = {
  isLoading: false,
  data: [],
  task: "",
  task_counts: {},
  all_assigned_task: [],
  all_created_tasks: [],
  all_notifications: [],
  notifications_count: "",
  notifications: "",
  mentions: [],
};

export default (state = initialState, action) => {
  switch (action.type) {
    case ActionTypes.GET_ALL_TASKS_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        data: action.payload,
      };

    case ActionTypes.GET_ALL_TASKS_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        data: [],
      };

    case ActionTypes.GET_ALL_TASKS_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        data: [],
      };

    case ActionTypes.GET_ALL_ASSIGNED_TASKS_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        all_assigned_task: action.payload,
      };

    case ActionTypes.GET_ALL_ASSIGNED_TASKS_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        all_assigned_task: [],
      };

    case ActionTypes.GET_ALL_ASSIGNED_TASKS_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        all_assigned_task: [],
      };

    case ActionTypes.GET_ALL_CREATED_TASKS_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        all_created_tasks: action.payload,
      };

    case ActionTypes.GET_ALL_CREATED_TASKS_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        all_created_tasks: [],
      };

    case ActionTypes.GET_ALL_CREATED_TASKS_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        all_created_tasks: [],
      };

    case ActionTypes.GET_ALL_NOTIFICATIONS_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        all_notifications: action.payload,
      };

    case ActionTypes.GET_ALL_NOTIFICATIONS_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        all_notifications: [],
      };

    case ActionTypes.GET_ALL_NOTIFICATIONS_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        all_notifications: [],
      };

    case ActionTypes.GET_NOTIFICATIONS_COUNT_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        notifications_count: action.payload,
      };

    case ActionTypes.GET_NOTIFICATIONS_COUNT_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        notifications_count: "",
      };

    case ActionTypes.GET_NOTIFICATIONS_COUNT_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        notifications_count: "",
      };

    case ActionTypes.CREATE_TASK_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        task: action.payload,
      };

    case ActionTypes.CREATE_TASK_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        task: "",
      };

    case ActionTypes.CREATE_TASK_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        task: "",
      };

    case ActionTypes.MARK_TASK_COMPLETE_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        task: action.payload,
      };

    case ActionTypes.MARK_TASK_COMPLETE_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        task: "",
      };

    case ActionTypes.MARK_TASK_COMPLETE_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        task: "",
      };

    case ActionTypes.MARK_ALL_NOTIFICATION_READ_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        notifications: action.payload,
      };

    case ActionTypes.MARK_ALL_NOTIFICATION_READ_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        notifications: "",
      };

    case ActionTypes.MARK_ALL_NOTIFICATION_READ_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        notifications: "",
      };

    case ActionTypes.MARK_NOTIFICATION_READ_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        notifications: action.payload,
      };

    case ActionTypes.MARK_NOTIFICATION_READ_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        notifications: "",
      };

    case ActionTypes.MARK_NOTIFICATION_READ_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        notifications: "",
      };

    case ActionTypes.DELETE_TASK_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        task: action.payload,
      };

    case ActionTypes.DELETE_TASK_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        task: "",
      };

    case ActionTypes.DELETE_TASK_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        task: "",
      };

    case ActionTypes.GET_TASK_MENTIONS_SUCCESS:
      return {
        ...state,
        isLoading: false,
        errMsg: null,
        successMsg: action.payload.message,
        mentions: action.payload,
      };

    case ActionTypes.GET_TASK_MENTIONS_PROCESSING:
      return {
        ...state,
        isLoading: true,
        errMsg: null,
        successMsg: "",
        mentions: [],
      };

    case ActionTypes.GET_TASK_MENTIONS_FAILED:
      return {
        ...state,
        isLoading: false,
        errMsg: action.payload,
        successMsg: "",
        mentions: [],
      };

    default:
      return state;
  }
};
