import * as ActionTypes from "../actionTypes";
import axios from "axios";
import { baseURL } from "../../components/Shared/baseURL";
import { toast } from "react-toastify";
import { DELETE_CALENDAR_SUCCESS } from "../actionTypes";

export const getCalendarEventsSuccess = (data) => ({
  type: ActionTypes.GET_CALENDAR_EVENT_SUCCESS,
  payload: data,
});

export const getCalendarEventsProcessing = () => ({
  type: ActionTypes.GET_CALENDAR_EVENT_PROCESSING,
});

export const getCalendarEventsFailed = (error) => ({
  type: ActionTypes.GET_CALENDAR_EVENT_FAILED,
  payload: error,
});

export const getCalendarEvents = (calendarIds) => (dispatch) => {
  // dispatch(getCalendarEventsProcessing());

  const token = localStorage.getItem("token");
  return axios
    .post(
      `${baseURL}api/google-calendar/all-user-events`,
      {
        calendar_id: calendarIds,
      },
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    )
    .then((response) => {
      dispatch(getCalendarEventsSuccess(response.data));
    })
    .catch((error) => {
      if (error.response !== undefined) {
        dispatch(getCalendarEventsFailed(error.response.data));
      }
    });
};

export const createCalendarEventSuccess = (data) => ({
  type: ActionTypes.CREATE_CALENDAR_EVENT_SUCCESS,
  payload: data,
});

export const createCalendarEventProcessing = () => ({
  type: ActionTypes.CREATE_CALENDAR_EVENT_PROCESSING,
});

export const createCalendarEventFailed = (error) => ({
  type: ActionTypes.CREATE_CALENDAR_EVENT_FAILED,
  payload: error,
});

export const createCalendarEvent = (values, setModal) => (dispatch) => {
  dispatch(createCalendarEventProcessing());

  const token = localStorage.getItem("token");
  return axios
    .post(`${baseURL}api/google-calendar/create-event`, values, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => {
      dispatch(createCalendarEventSuccess(response.data));
      // dispatch(getCalendarEvents());
      setModal(false);
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
      if (error.response !== undefined) {
        dispatch(createCalendarEventFailed(error.response.data));
        toast.error(error.response.data.message, {
          position: "top-right",
          autoClose: 3000,
          hideProgressBar: true,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true,
          progress: undefined,
        });
      }
    });
};

export const updateCalendarEventSuccess = (data) => ({
  type: ActionTypes.UPDATE_CALENDAR_EVENT_SUCCESS,
  payload: data,
});

export const updateCalendarEventProcessing = () => ({
  type: ActionTypes.UPDATE_CALENDAR_EVENT_PROCESSING,
});

export const updateCalendarEventFailed = (error) => ({
  type: ActionTypes.UPDATE_CALENDAR_EVENT_FAILED,
  payload: error,
});

export const updateCalendarEvent = (values, setModal) => (dispatch) => {
  dispatch(updateCalendarEventProcessing());

  const token = localStorage.getItem("token");
  return axios
    .post(`${baseURL}api/google-calendar/update-event`, values, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => {
      dispatch(updateCalendarEventSuccess(response.data));
      setModal(false);
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
      if (error.response !== undefined) {
        dispatch(updateCalendarEventFailed(error.response.data));
        toast.error(error.response.data.message, {
          position: "top-right",
          autoClose: 3000,
          hideProgressBar: true,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true,
          progress: undefined,
        });
      }
    });
};

export const deleteCalendarEventSuccess = (data) => ({
  type: ActionTypes.DELETE_CALENDAR_EVENT_SUCCESS,
  payload: data,
});

export const deleteCalendarEventProcessing = () => ({
  type: ActionTypes.DELETE_CALENDAR_EVENT_PROCESSING,
});

export const deleteCalendarEventFailed = (error) => ({
  type: ActionTypes.DELETE_CALENDAR_EVENT_FAILED,
  payload: error,
});

export const deleteCalendarEvent = (event_id) => (dispatch) => {
  dispatch(deleteCalendarEventProcessing());

  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/google-calendar/delete-event/${event_id}`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => {
      dispatch(deleteCalendarEventSuccess(response.data));
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
      if (error.response !== undefined) {
        dispatch(deleteCalendarEventFailed(error.response.data));
        toast.error(error.response.data.message, {
          position: "top-right",
          autoClose: 3000,
          hideProgressBar: true,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true,
          progress: undefined,
        });
      }
    });
};

export const getCalendarsSuccess = (data) => ({
  type: ActionTypes.GET_CALENDARS_SUCCESS,
  payload: data,
});

export const getCalendarsProcessing = () => ({
  type: ActionTypes.GET_CALENDARS_PROCESSING,
});

export const getCalendarsFailed = (error) => ({
  type: ActionTypes.GET_CALENDARS_FAILED,
  payload: error,
});

export const getCalendars = () => (dispatch) => {
  dispatch(getCalendarsProcessing());

  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/google-calendar/get-calendars`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => {
      dispatch(getCalendarsSuccess(response.data));
    })
    .catch((error) => {
      if (error.response !== undefined) {
        dispatch(getCalendarsFailed(error.response.data));
      }
    });
};

export const createNewCalendarSuccess = (data) => ({
  type: ActionTypes.CREATE_NEW_CALENDAR_SUCCESS,
  payload: data,
});

export const createNewCalendarProcessing = () => ({
  type: ActionTypes.CREATE_NEW_CALENDAR_PROCESSING,
});

export const createNewCalendarFailed = (error) => ({
  type: ActionTypes.CREATE_NEW_CALENDAR_FAILED,
  payload: error,
});

export const createNewCalendar =
  (values, setNewCalendarModal, fetchCalendarEvents, setErrors) =>
  (dispatch) => {
    dispatch(createNewCalendarProcessing());

    const token = localStorage.getItem("token");
    return axios
      .post(`${baseURL}api/google-calendar/create-calendar`, values, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
      .then((response) => {
        dispatch(createNewCalendarSuccess(response.data));
        fetchCalendarEvents();
        setNewCalendarModal(false);
        setErrors({});
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
        if (error.response !== undefined) {
          dispatch(createNewCalendarFailed(error.response.data));
          setErrors(error.response.data.errors);
          toast.error(error.response.data.message, {
            position: "top-right",
            autoClose: 3000,
            hideProgressBar: true,
            closeOnClick: true,
            pauseOnHover: true,
            draggable: true,
            progress: undefined,
          });
        }
      });
  };

export const deleteCalendarSuccess = (data) => ({
  type: ActionTypes.DELETE_CALENDAR_SUCCESS,
  payload: data,
});

export const deleteCalendarProcessing = () => ({
  type: ActionTypes.DELETE_CALENDAR_PROCESSING,
});

export const deleteCalendarFailed = (error) => ({
  type: ActionTypes.DELETE_CALENDAR_FAILED,
  payload: error,
});

export const deleteCalendar = (calendarID) => (dispatch) => {
  dispatch(deleteCalendarProcessing());

  const token = localStorage.getItem("token");
  return axios
    .post(
      `${baseURL}api/google-calendar/delete-calendar`,
      { id: calendarID },
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    )
    .then((response) => {
      dispatch(deleteCalendarSuccess(response.data));
      dispatch(getCalendars());
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
      if (error.response !== undefined) {
        dispatch(deleteCalendarFailed(error.response.data));
        toast.error(error.response.data.message, {
          position: "top-right",
          autoClose: 3000,
          hideProgressBar: true,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true,
          progress: undefined,
        });
      }
    });
};

export const shareCalendarSuccess = (data) => ({
  type: ActionTypes.SHARE_CALENDAR_SUCCESS,
  payload: data,
});

export const shareCalendarProcessing = () => ({
  type: ActionTypes.SHARE_CALENDAR_PROCESSING,
});

export const shareCalendarFailed = (error) => ({
  type: ActionTypes.SHARE_CALENDAR_FAILED,
  payload: error,
});

export const shareCalendar =
  (values, setShareCalendarModal, shareCalendarID) => (dispatch) => {
    dispatch(shareCalendarProcessing());

    const token = localStorage.getItem("token");
    return axios
      .post(`${baseURL}api/google-calendar/share-calendar`, values, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
      .then((response) => {
        dispatch(shareCalendarSuccess(response.data));
        dispatch(shareCalendarWith(shareCalendarID));
        setShareCalendarModal(false);
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
        if (error.response !== undefined) {
          dispatch(shareCalendarFailed(error.response.data));
          toast.error(error.response.data.message, {
            position: "top-right",
            autoClose: 3000,
            hideProgressBar: true,
            closeOnClick: true,
            pauseOnHover: true,
            draggable: true,
            progress: undefined,
          });
        }
      });
  };

export const shareCalendarWithSuccess = (data) => ({
  type: ActionTypes.SHARE_CALENDAR_WITH_SUCCESS,
  payload: data,
});

export const shareCalendarWithProcessing = () => ({
  type: ActionTypes.SHARE_CALENDAR_WITH_PROCESSING,
});

export const shareCalendarWithFailed = (error) => ({
  type: ActionTypes.SHARE_CALENDAR_WITH_FAILED,
  payload: error,
});

export const shareCalendarWith = (calendarID) => (dispatch) => {
  dispatch(shareCalendarWithProcessing());

  const token = localStorage.getItem("token");
  return axios
    .post(
      `${baseURL}api/google-calendar/shared-with`,
      { google_calendar_id: calendarID },
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    )
    .then((response) => {
      dispatch(shareCalendarWithSuccess(response.data));
    })
    .catch((error) => {
      if (error.response !== undefined) {
        dispatch(shareCalendarWithFailed(error.response.data));
        toast.error(error.response.data.message, {
          position: "top-right",
          autoClose: 3000,
          hideProgressBar: true,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true,
          progress: undefined,
        });
      }
    });
};

export const deleteShareCalendarWithSuccess = (data) => ({
  type: ActionTypes.DELETE_SHARE_CALENDAR_WITH_SUCCESS,
  payload: data,
});

export const deleteShareCalendarWithProcessing = () => ({
  type: ActionTypes.DELETE_SHARE_CALENDAR_WITH_PROCESSING,
});

export const deleteShareCalendarWithFailed = (error) => ({
  type: ActionTypes.DELETE_SHARE_CALENDAR_WITH_FAILED,
  payload: error,
});

export const deleteShareCalendarWith = (calendarID, userID) => (dispatch) => {
  dispatch(deleteShareCalendarWithProcessing());

  const token = localStorage.getItem("token");
  return axios
    .post(
      `${baseURL}api/google-calendar/remove-shared-with`,
      {
        google_calendar_id: calendarID,
        user_pr_id: userID,
      },
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    )
    .then((response) => {
      dispatch(deleteShareCalendarWithSuccess(response.data));
      dispatch(shareCalendarWith(calendarID));
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
      if (error.response !== undefined) {
        dispatch(deleteShareCalendarWithFailed(error.response.data));
        toast.error(error.response.data.message, {
          position: "top-right",
          autoClose: 3000,
          hideProgressBar: true,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true,
          progress: undefined,
        });
      }
    });
};
