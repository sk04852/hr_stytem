import * as ActionTypes from "../actionTypes";
import axios from "axios";
import { toast } from "react-toastify";
import { baseURL } from "../../components/Shared/baseURL";

export const getUsersSuccess = (data) => ({
  type: ActionTypes.GET_USERS_SUCCESS,
  payload: data,
});

export const getUsersProcessing = () => ({
  type: ActionTypes.GET_USERS_PROCESSING,
});

export const getUsersFailed = (error) => ({
  type: ActionTypes.GET_USERS_FAILED,
  payload: error,
});

export const getAllUsers = () => (dispatch) => {
  dispatch(getUsersProcessing());
  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/users`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => dispatch(getUsersSuccess(response)))
    .catch((error) => dispatch(getUsersFailed(error)));
};

export const getUserProfileSuccess = (data) => ({
  type: ActionTypes.GET_USERS_PROFILE_SUCCESS,
  payload: data,
});

export const getUserProfile = () => (dispatch) => {
  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/auth/profile`, {
      headers: { Authorization: `Bearer ${token}` },
    })
    .then((response) => dispatch(getUserProfileSuccess(response)));
};

export const getSingleUserSuccess = (data) => ({
  type: ActionTypes.GET_SINGLE_USER_SUCCESS,
  payload: data,
});

export const getSingleUserProcessing = () => ({
  type: ActionTypes.GET_SINGLE_USER_PROCESSING,
});

export const getSingleUserFailed = (error) => ({
  type: ActionTypes.GET_SINGLE_USER_FAILED,
  payload: error,
});

export const getSingleUser = (id) => (dispatch) => {
  dispatch(getSingleUserProcessing());
  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/users/` + id, {
      headers: { Authorization: `Bearer ${token}` },
    })
    .then((response) => dispatch(getSingleUserSuccess(response)))
    .catch((error) => getSingleUserFailed(error));
};

export const loginUserSuccess = (data) => ({
  type: ActionTypes.LOGIN_USER_SUCCESS,
  payload: data,
});

export const loginUserProcessing = () => ({
  type: ActionTypes.LOGIN_USER_PROCESSING,
});

export const loginUserFailed = (error) => ({
  type: ActionTypes.LOGIN_USER_FAILED,
  payload: error,
});

export const loginUser = (values, history) => (dispatch) => {
  dispatch(loginUserProcessing());

  return axios
    .post(`${baseURL}api/auth/login`, values)
    .then((response) => {
      localStorage.setItem("token", response.data.token);
      dispatch(loginUserSuccess(response));

      let userRole = response.data.user.roles[0]?.name
      if (userRole === 'Super Admin' || userRole === 'Main Admin' || userRole === 'Admin'){
        history.push("/dashboard/task");
      } else if (userRole === 'Recruiter'){
        history.push("/dashboard/todo");
      }
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
      dispatch(loginUserFailed(error));
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

export const logoutUserSuccess = (data) => ({
  type: ActionTypes.LOGOUT_USER_SUCCESS,
  payload: data,
});

export const logoutUserProcessing = () => ({
  type: ActionTypes.LOGOUT_USER_PROCESSING,
});

export const logoutUserFailed = (error) => ({
  type: ActionTypes.LOGOUT_USER_FAILED,
  payload: error,
});

export const logoutUser = (history) => (dispatch) => {
  dispatch(logoutUserProcessing());

  const token = localStorage.getItem("token");
  return axios
    .post(
      `${baseURL}api/auth/logout`,
      {},
      {
        headers: { Authorization: `Bearer ${token}` },
      }
    )
    .then((response) => {
      localStorage.removeItem("token");
      localStorage.removeItem("persist:root");
      localStorage.removeItem("persist:users");
      dispatch(logoutUserSuccess(response));
      history.push("/");
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
      dispatch(logoutUserFailed(error));
      toast.error("Something went wrong. Please Try Again.", {
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

export const autoLogoutUser = () => (dispatch) => {
  localStorage.removeItem("token");
  localStorage.removeItem("persist:root");
  dispatch(logoutUserSuccess({ message: "User session expired" }));
  // history.push("/");
};

export const changePasswordSuccess = (data) => ({
  type: ActionTypes.CHANGE_USER_PASSWORD_SUCCESS,
  payload: data,
});

export const changePasswordProcessing = () => ({
  type: ActionTypes.CHANGE_USER_PASSWORD_PROCESSING,
});

export const changePasswordFailed = (error) => ({
  type: ActionTypes.CHANGE_USER_PASSWORD_FAILED,
  payload: error,
});

export const changePassword = (values, history) => (dispatch) => {
  dispatch(changePasswordProcessing());

  let token = localStorage.getItem("token");

  return axios
    .post(`${baseURL}api/auth/change-password`, values, {
      headers: { Authorization: `Bearer ${token}` },
    })
    .then((response) => {
      dispatch(changePasswordSuccess(response));
      toast.success(response.data.message, {
        position: "top-right",
        autoClose: 3000,
        hideProgressBar: true,
        closeOnClick: true,
        pauseOnHover: true,
        draggable: true,
        progress: undefined,
      });
      dispatch(logoutUser(history));
    })
    .catch((error) => {
      if (error.response !== undefined) {
        dispatch(changePasswordFailed(error.response));
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

export const updateUserProfileSuccess = (data) => ({
  type: ActionTypes.UPDATE_USERS_PROFILE_SUCCESS,
  payload: data,
});

export const updateUserProfileProcessing = () => ({
  type: ActionTypes.UPDATE_USERS_PROFILE_PROCESSING,
});

export const updateUserProfileFailed = (error) => ({
  type: ActionTypes.UPDATE_USERS_PROFILE_FAILED,
  payload: error,
});

export const updateUserProfile =
  (values, setEditProfile, setErrors) => (dispatch) => {
    dispatch(updateUserProfileProcessing());

    const token = localStorage.getItem("token");
    return axios
      .post(`${baseURL}api/auth/update-profile`, values, {
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "multipart/form-data",
        },
      })
      .then((response) => {
        dispatch(updateUserProfileSuccess(response.data));
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
        dispatch(getUserProfile());
      })
      .catch((error) => {
        dispatch(updateUserProfileFailed(error.response));
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

export const addNewUserSuccess = (data) => ({
  type: ActionTypes.ADD_NEW_USER_SUCCESS,
  payload: data,
});

export const addNewUserProcessing = () => ({
  type: ActionTypes.ADD_NEW_USER_PROCESSING,
});

export const addNewUserFailed = (error) => ({
  type: ActionTypes.ADD_NEW_USER_FAILED,
  payload: error,
});

export const addNewUser = (values, setModal, setErrors) => (dispatch) => {
  dispatch(addNewUserProcessing());

  const token = localStorage.getItem("token");
  return axios
    .post(`${baseURL}api/users`, values, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => {
      dispatch(addNewUserSuccess(response));
      dispatch(getAllUsers());
      setModal(false);
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
      dispatch(addNewUserFailed(error.response));
      if (error.response.data.errors) {
        setErrors(error.response.data.errors);
      }
      toast.error("Add User Failed", {
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

export const updateUserSuccess = (data) => ({
  type: ActionTypes.UPDATE_USER_SUCCESS,
  payload: data,
});

export const updateUserProcessing = () => ({
  type: ActionTypes.UPDATE_USER_PROCESSING,
});

export const updateUserFailed = (error) => ({
  type: ActionTypes.UPDATE_USER_FAILED,
  payload: error,
});

export const updateUser = (values, token) => (dispatch) => {
  dispatch(updateUserProcessing());

  return axios
    .post(`${baseURL}api/users/update`, values, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => {
      dispatch(updateUserSuccess(response));
      document.getElementById("update-user-form").reset();
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
      dispatch(updateUserFailed(error));
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

export const deleteUserSuccess = (data) => ({
  type: ActionTypes.DELETE_USER_SUCCESS,
  payload: data,
});

export const deleteUserProcessing = () => ({
  type: ActionTypes.DELETE_USER_PROCESSING,
});

export const deleteUserFailed = (error) => ({
  type: ActionTypes.DELETE_USER_FAILED,
  payload: error,
});

export const deleteUser = (deleteID, setDeleteID, setModal) => (dispatch) => {
  dispatch(deleteUserProcessing());

  const token = localStorage.getItem("token");
  return axios
    .post(
      `${baseURL}api/users/delete`,
      {
        users: deleteID,
      },
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    )
    .then((response) => {
      dispatch(deleteUserSuccess(response));
      setDeleteID([]);
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
      dispatch(getAllUsers());
    })
    .catch((error) => {
      dispatch(deleteUserFailed(error));
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
