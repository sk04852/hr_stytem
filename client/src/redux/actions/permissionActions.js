import * as ActionTypes from "../actionTypes";
import axios from "axios";
import { baseURL } from "../../components/Shared/baseURL";
import { toast } from "react-toastify";
import { logoutUser } from "./usersAction";

export const getPermissionsSuccess = (data) => ({
  type: ActionTypes.GET_PERMISSIONS_SUCCESS,
  payload: data,
});

export const getPermissionsProcessing = () => ({
  type: ActionTypes.GET_PERMISSIONS_PROCESSING,
});

export const getPermissionsFailed = (error) => ({
  type: ActionTypes.GET_PERMISSIONS_FAILED,
  payload: error,
});

export const getPermissions = (setPermissions) => (dispatch) => {
  dispatch(getPermissionsProcessing());
  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/users/permissions`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => {
      dispatch(getPermissionsSuccess(response.data));
      setPermissions(response.data);
    })
    .catch((error) => {
      dispatch(getPermissionsFailed(error));
    });
};

export const assignPermissionsToRoleSuccess = (data) => ({
  type: ActionTypes.ASSIGN_PERMISSIONS_TO_ROLE_SUCCESS,
  payload: data,
});

export const assignPermissionsToRoleProcessing = () => ({
  type: ActionTypes.ASSIGN_PERMISSIONS_TO_ROLE_PROCESSING,
});

export const assignPermissionsToRoleFailed = (error) => ({
  type: ActionTypes.ASSIGN_PERMISSIONS_TO_ROLE_FAILED,
  payload: error,
});

export const assignPermissionsToRole = (values, history) => (dispatch) => {
  dispatch(assignPermissionsToRoleProcessing());
  const token = localStorage.getItem("token");
  return axios
    .post(`${baseURL}api/users/roles/assign-permissions`, values, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => {
      dispatch(assignPermissionsToRoleSuccess(response.data));
      toast.success(response.data.permissions, {
        position: "top-right",
        autoClose: 3000,
        hideProgressBar: true,
        closeOnClick: true,
        pauseOnHover: true,
        draggable: true,
        progress: undefined,
      });
      // setTimeout(() => {
      //   dispatch(logoutUser(history));
      //   setTimeout(() => {
      //     toast.success("Login Again To See The Changings.", {
      //       position: "top-right",
      //       autoClose: 3000,
      //       hideProgressBar: true,
      //       closeOnClick: true,
      //       pauseOnHover: true,
      //       draggable: true,
      //       progress: undefined,
      //     });
      //   }, 1000);
      // }, 3000);
    })
    .catch((error) => {
      dispatch(assignPermissionsToRoleFailed(error));
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

export const assignPermissionsToUserSuccess = (data) => ({
  type: ActionTypes.ASSIGN_PERMISSIONS_TO_USER_SUCCESS,
  payload: data,
});

export const assignPermissionsToUserProcessing = () => ({
  type: ActionTypes.ASSIGN_PERMISSIONS_TO_USER_PROCESSING,
});

export const assignPermissionsToUserFailed = (error) => ({
  type: ActionTypes.ASSIGN_PERMISSIONS_TO_USER_FAILED,
  payload: error,
});

export const assignPermissionsToUser = (values, history) => (dispatch) => {
  dispatch(assignPermissionsToUserProcessing());
  const token = localStorage.getItem("token");
  return axios
    .post(`${baseURL}api/users/permissions`, values, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => {
      dispatch(assignPermissionsToUserSuccess(response.data));
      toast.success("Assign Permissions To User Success.", {
        position: "top-right",
        autoClose: 3000,
        hideProgressBar: true,
        closeOnClick: true,
        pauseOnHover: true,
        draggable: true,
        progress: undefined,
      });
      // setTimeout(() => {
      //   dispatch(logoutUser(history));
      //   setTimeout(() => {
      //     toast.success("Login Again To See The Changings.", {
      //       position: "top-right",
      //       autoClose: 3000,
      //       hideProgressBar: true,
      //       closeOnClick: true,
      //       pauseOnHover: true,
      //       draggable: true,
      //       progress: undefined,
      //     });
      //   }, 1000);
      // }, 3000);
    })
    .catch((error) => {
      dispatch(assignPermissionsToUserFailed(error));
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

export const changeUserRole = (values) => (dispatch) => {
  const token = localStorage.getItem("token");
  return axios
    .post(`${baseURL}api/users/change-role`, values, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => {
      toast.success("Role Changed.", {
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
      dispatch(assignPermissionsToUserFailed(error));
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
