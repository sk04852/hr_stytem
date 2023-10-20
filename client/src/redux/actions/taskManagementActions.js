import * as ActionTypes from "../actionTypes";
import axios from "axios";
import { baseURL } from "../../components/Shared/baseURL";
import { toast } from "react-toastify";
import { EditorState } from "draft-js";

export const getAllTasksSuccess = (data) => ({
  type: ActionTypes.GET_ALL_TASKS_SUCCESS,
  payload: data,
});

export const getAllTasksProcessing = () => ({
  type: ActionTypes.GET_ALL_TASKS_PROCESSING,
});

export const getAllTasksFailed = (error) => ({
  type: ActionTypes.GET_ALL_TASKS_FAILED,
  payload: error,
});

export const getAllTasks = () => (dispatch) => {
  dispatch(getAllTasksProcessing());
  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/hr-tasks`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => {
      dispatch(getAllTasksSuccess(response.data));
    })
    .catch((error) => {
      dispatch(getAllTasksFailed(error));
    });
};

export const getAllTasksCountSuccess = (data) => ({
  type: ActionTypes.GET_ALL_TASKS_COUNTS_SUCCESS,
  payload: data,
});

export const getAllTasksCountProcessing = () => ({
  type: ActionTypes.GET_ALL_TASKS_COUNTS_PROCESSING,
});

export const getAllTasksCountFailed = (error) => ({
  type: ActionTypes.GET_ALL_TASKS_COUNTS_FAILED,
  payload: error,
});

export const getAllTasksCount = () => (dispatch) => {
  dispatch(getAllTasksCountProcessing());
  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/dashboard`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => dispatch(getAllTasksCountSuccess(response.data)))
    .catch((error) => dispatch(getAllTasksCountFailed(error.response.data)));
};

export const getAllAssignedTaskSuccess = (data) => ({
  type: ActionTypes.GET_ALL_ASSIGNED_TASKS_SUCCESS,
  payload: data,
});

export const getAllAssignedTasksProcessing = () => ({
  type: ActionTypes.GET_ALL_ASSIGNED_TASKS_PROCESSING,
});

export const getAllAssignedTasksFailed = (error) => ({
  type: ActionTypes.GET_ALL_ASSIGNED_TASKS_FAILED,
  payload: error,
});

export const getAllAssignedTasks = (pageNumber) => (dispatch) => {
  // dispatch(getAllAssignedTasksProcessing());
  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/hr-tasks/get-assigned-tasks?page=${pageNumber}`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => dispatch(getAllAssignedTaskSuccess(response.data)))
    .catch((error) => dispatch(getAllAssignedTasksFailed(error)));
};

export const getAllCreatedTaskSuccess = (data) => ({
  type: ActionTypes.GET_ALL_CREATED_TASKS_SUCCESS,
  payload: data,
});

export const getAllCreatedTasksProcessing = () => ({
  type: ActionTypes.GET_ALL_CREATED_TASKS_PROCESSING,
});

export const getAllCreatedTasksFailed = (error) => ({
  type: ActionTypes.GET_ALL_CREATED_TASKS_FAILED,
  payload: error,
});

export const getAllCreatedTasks = (pageNumber) => (dispatch) => {
  // dispatch(getAllCreatedTasksProcessing());
  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/hr-tasks/get-created-tasks?page=${pageNumber}`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => {
      dispatch(getAllCreatedTaskSuccess(response.data));
    })
    .catch((error) => {
      if (error.response !== undefined) {
        dispatch(getAllCreatedTasksFailed(error.response.data));
      }
    });
};

export const createTaskSuccess = (data) => ({
  type: ActionTypes.CREATE_TASK_SUCCESS,
  payload: data,
});

export const createTaskProcessing = () => ({
  type: ActionTypes.CREATE_TASK_PROCESSING,
});

export const createTaskFailed = (error) => ({
  type: ActionTypes.CREATE_TASK_FAILED,
  payload: error,
});

export const createTask =
  (values, setModal, setErrors, setEditorState, EditorState) => (dispatch) => {
    dispatch(createTaskProcessing());
    const token = localStorage.getItem("token");
    return axios
      .post(`${baseURL}api/hr-tasks`, values, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
      .then((response) => {
        dispatch(createTaskSuccess(response.data));
        document.getElementById("add-new-task-form").reset();
        dispatch(getAllAssignedTasks(1));
        dispatch(getAllCreatedTasks(1));
        dispatch(getUnreadNotificationsCount());
        setModal(false);
        setErrors({});
        setEditorState(EditorState.createEmpty());
        toast.success(response.data.message, {
          position: "top-right",
          autoClose: 3000,
          hideProgressBar: true,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true,
          progress: undefined,
        });
      })
      .catch((error) => {
        dispatch(createTaskFailed(error.response));
        if (error.response.data.errors) {
          setErrors(error.response.data.errors);
        }
        toast.error(error.response.data.message, {
          position: "top-right",
          autoClose: 3000,
          hideProgressBar: true,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true,
          progress: undefined,
        });
      });
  };

export const markTaskCompleteSuccess = (data) => ({
  type: ActionTypes.MARK_TASK_COMPLETE_SUCCESS,
  payload: data,
});

export const markTaskCompleteProcessing = () => ({
  type: ActionTypes.MARK_TASK_COMPLETE_PROCESSING,
});

export const markTaskCompleteFailed = (error) => ({
  type: ActionTypes.MARK_TASK_COMPLETE_FAILED,
  payload: error,
});

export const markTaskComplete = (id, setChecked, pageNumber) => (dispatch) => {
  dispatch(markTaskCompleteProcessing());
  const token = localStorage.getItem("token");
  return axios
    .post(
      `${baseURL}api/hr-tasks/mark-complete`,
      {
        id: id,
      },
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    )
    .then((response) => {
      dispatch(markTaskCompleteSuccess(response.data));
      setChecked(true);
      dispatch(getAllAssignedTasks(pageNumber));
      dispatch(getAllCreatedTasks(pageNumber));
      setChecked(false);
      toast.success(response.data.message, {
        position: "top-right",
        autoClose: 3000,
        hideProgressBar: true,
        closeOnClick: true,
        pauseOnHover: true,
        draggable: true,
        progress: undefined,
      });
    })
    .catch((error) => {
      dispatch(markTaskCompleteFailed(error));
      toast.error(error.response.data.message, {
        position: "top-right",
        autoClose: 3000,
        hideProgressBar: true,
        closeOnClick: true,
        pauseOnHover: true,
        draggable: true,
        progress: undefined,
      });
    });
};

export const deleteTaskSuccess = (data) => ({
  type: ActionTypes.DELETE_TASK_SUCCESS,
  payload: data,
});

export const deleteTaskProcessing = () => ({
  type: ActionTypes.DELETE_TASK_PROCESSING,
});

export const deleteTaskFailed = (error) => ({
  type: ActionTypes.DELETE_TASK_FAILED,
  payload: error,
});

export const deleteTask = (id, pageNumber) => (dispatch) => {
  dispatch(deleteTaskProcessing());
  const token = localStorage.getItem("token");
  return axios
    .post(
      `${baseURL}api/hr-tasks/delete`,
      {
        id: id,
      },
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    )
    .then((response) => {
      dispatch(deleteTaskSuccess(response.data));
      dispatch(getAllCreatedTasks(pageNumber));
      toast.success(response.data.message, {
        position: "top-right",
        autoClose: 3000,
        hideProgressBar: true,
        closeOnClick: true,
        pauseOnHover: true,
        draggable: true,
        progress: undefined,
      });
    })
    .catch((error) => {
      dispatch(deleteTaskFailed(error));
      toast.error(error.response.data.message, {
        position: "top-right",
        autoClose: 3000,
        hideProgressBar: true,
        closeOnClick: true,
        pauseOnHover: true,
        draggable: true,
        progress: undefined,
      });
    });
};

// NOTIFICATION
export const getAllNotificationsSuccess = (data) => ({
  type: ActionTypes.GET_ALL_NOTIFICATIONS_SUCCESS,
  payload: data,
});

export const getAllNotificationsProcessing = () => ({
  type: ActionTypes.GET_ALL_NOTIFICATIONS_PROCESSING,
});

export const getAllNotificationsFailed = (error) => ({
  type: ActionTypes.GET_ALL_NOTIFICATIONS_FAILED,
  payload: error,
});

export const getAllNotifications = (offset) => (dispatch) => {
  dispatch(getAllNotificationsProcessing());
  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/users/notifications/all/` + offset, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => dispatch(getAllNotificationsSuccess(response.data)))
    .catch((error) => {
      if (error.response !== undefined) {
        dispatch(getAllNotificationsFailed(error.response.data));
      }
    });
};

export const getUnreadNotificationsCountSuccess = (data) => ({
  type: ActionTypes.GET_NOTIFICATIONS_COUNT_SUCCESS,
  payload: data,
});

export const getUnreadNotificationsCountProcessing = () => ({
  type: ActionTypes.GET_NOTIFICATIONS_COUNT_PROCESSING,
});

export const getUnreadNotificationsCountFailed = (error) => ({
  type: ActionTypes.GET_NOTIFICATIONS_COUNT_FAILED,
  payload: error,
});

export const getUnreadNotificationsCount = () => (dispatch) => {
  dispatch(getUnreadNotificationsCountProcessing());
  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/users/notifications/unread-count`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => {
      dispatch(getUnreadNotificationsCountSuccess(response.data));
    })
    .catch((error) => {
      if (error.response !== undefined) {
        dispatch(getUnreadNotificationsCountFailed(error.response.data));
      } else {
      }
    });
};

export const markAllNotificationReadSuccess = (data) => ({
  type: ActionTypes.MARK_ALL_NOTIFICATION_READ_SUCCESS,
  payload: data,
});

export const markAllNotificationReadProcessing = () => ({
  type: ActionTypes.MARK_ALL_NOTIFICATION_READ_PROCESSING,
});

export const markAllNotificationReadFailed = (error) => ({
  type: ActionTypes.MARK_ALL_NOTIFICATION_READ_FAILED,
  payload: error,
});

export const markAllNotificationRead =
  (fetchAllNotifications, fetchUnreadNotification) => (dispatch) => {
    dispatch(markAllNotificationReadProcessing());
    const token = localStorage.getItem("token");
    return axios
      .get(`${baseURL}api/users/notifications/mark-read/all`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
      .then((response) => {
        dispatch(markAllNotificationReadSuccess(response.data));
        fetchAllNotifications();
        fetchUnreadNotification();
        // dispatch(getAllNotifications(3));
        // dispatch(getUnreadNotificationsCount());
      })
      .catch((error) => {
        if (error.response !== undefined) {
          dispatch(markAllNotificationReadFailed(error.response.data));
        }
      });
  };

export const markNotificationReadSuccess = (data) => ({
  type: ActionTypes.MARK_NOTIFICATION_READ_SUCCESS,
  payload: data,
});

export const markNotificationReadProcessing = () => ({
  type: ActionTypes.MARK_NOTIFICATION_READ_PROCESSING,
});

export const markNotificationReadFailed = (error) => ({
  type: ActionTypes.MARK_NOTIFICATION_READ_FAILED,
  payload: error,
});

export const markNotificationRead = (id) => (dispatch) => {
  dispatch(markNotificationReadProcessing());
  const token = localStorage.getItem("token");
  return axios
    .post(
      `${baseURL}api/users/notifications/mark-read`,
      { notification_id: id },
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    )
    .then((response) => {
      dispatch(markNotificationReadSuccess(response.data));
      dispatch(getAllNotifications());
      dispatch(getUnreadNotificationsCount());
    })
    .catch((error) => {
      if (error.response !== undefined) {
        dispatch(markNotificationReadFailed(error.response.data));
      }
    });
};

export const getMentionsSuccess = (data) => ({
  type: ActionTypes.GET_TASK_MENTIONS_SUCCESS,
  payload: data,
});

export const getMentionsProcessing = () => ({
  type: ActionTypes.GET_TASK_MENTIONS_PROCESSING,
});

export const getMentionsFailed = (error) => ({
  type: ActionTypes.GET_TASK_MENTIONS_FAILED,
  payload: error,
});

export const getMentions = (mentionSearchTerm) => (dispatch) => {
  dispatch(getMentionsProcessing());
  const token = localStorage.getItem("token");
  return axios
    .post(
      `${baseURL}api/hr-tasks/mention`,
      {
        search: mentionSearchTerm,
      },
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    )
    .then((response) => {
      dispatch(getMentionsSuccess(response.data));
    })
    .catch((error) => {
      dispatch(getMentionsFailed(error));
    });
};
