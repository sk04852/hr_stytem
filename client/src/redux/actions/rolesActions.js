import * as ActionTypes from "../actionTypes";
import axios from "axios";
import { baseURL } from "../../components/Shared/baseURL";
import { toast } from "react-toastify";

export const getRolesSuccess = (data) => ({
  type: ActionTypes.GET_ROLES_SUCCESS,
  payload: data,
});

export const getRolesProcessing = () => ({
  type: ActionTypes.GET_ROLES_PROCESSING,
});

export const getRolesFailed = (error) => ({
  type: ActionTypes.GET_ROLES_FAILED,
  payload: error,
});

export const getRoles = () => (dispatch) => {
  dispatch(getRolesProcessing());
  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/users/roles`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => {
      dispatch(getRolesSuccess(response));
    })
    .catch((error) => {
      if (error.response !== undefined) {
        dispatch(getRolesFailed(error));
      }
    });
};

export const getSingleRoleSuccess = (data) => ({
  type: ActionTypes.GET_SINGLE_ROLE_SUCCESS,
  payload: data,
});

export const getSingleRoleProcessing = () => ({
  type: ActionTypes.GET_SINGLE_ROLE_PROCESSING,
});

export const getSingleRoleFailed = (error) => ({
  type: ActionTypes.GET_SINGLE_ROLE_FAILED,
  payload: error,
});

export const getSingleRole = (id, setRoleName, setFormValues) => (dispatch) => {
  dispatch(getSingleRoleProcessing());
  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/users/roles/` + id + `/permissions`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => {
      dispatch(getSingleRoleSuccess(response.data));
      setRoleName(response.data.role.name);
      let assignedPermssions = [];
      if (
        Array.isArray(response.data.role.permissions) &&
        response.data.role.permissions.length > 0
      ) {
        for (let i = 0; i < response.data.role.permissions.length; i++) {
          assignedPermssions.push(
            response.data.role.permissions[i].id.toString()
          );
        }
      }
      setFormValues({
        role_id: response.data.role.id,
        permissions: assignedPermssions,
      });
    })
    .catch((error) => {
      dispatch(getSingleRoleFailed(error));
    });
};

export const getSingleRole2 = (id, user_id, setFormValues) => (dispatch) => {
  dispatch(getSingleRoleProcessing());
  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/users/roles/` + id + `/permissions`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => {
      let assignedPermssions = [];
      if (
        Array.isArray(response.data.role.permissions) &&
        response.data.role.permissions.length > 0
      ) {
        for (let i = 0; i < response.data.role.permissions.length; i++) {
          assignedPermssions.push(response.data.role.permissions[i].name);
        }
      }
      setFormValues({
        user_id: user_id,
        permissions: assignedPermssions,
      });
    })
    .catch((error) => {
      dispatch(getSingleRoleFailed(error));
    });
};

export const postUserRolesSuccess = (data) => ({
  type: ActionTypes.POST_USER_ROLES_SUCCESS,
  payload: data,
});

export const postUserRolesProcessing = () => ({
  type: ActionTypes.POST_USER_ROLES_PROCESSING,
});

export const postUserRolesFailed = (error) => ({
  type: ActionTypes.POST_USER_ROLES_FAILED,
  payload: error,
});

export const postUserRoles = (values) => (dispatch) => {
  dispatch(postUserRolesProcessing());
  const token = localStorage.getItem("token");
  return axios
    .post(`${baseURL}api/users/roles`, values, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => {
      dispatch(postUserRolesSuccess(response.data));
      dispatch(getRoles());
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
      dispatch(postUserRolesFailed(error));
      console.log(error);
      toast.error("Please Try Again", {
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

export const updateUserRolesSuccess = (data) => ({
  type: ActionTypes.DELETE_USER_ROLES_SUCCESS,
  payload: data,
});

export const updateUserRolesProcessing = () => ({
  type: ActionTypes.DELETE_USER_ROLES_PROCESSING,
});

export const updateUserRolesFailed = (error) => ({
  type: ActionTypes.DELETE_USER_ROLES_FAILED,
  payload: error,
});

export const updateUserRoles = (id, name) => (dispatch) => {
  dispatch(updateUserRolesProcessing());
  const token = localStorage.getItem("token");
  return axios
    .post(
      `${baseURL}api/users/roles/update`,
      {
        id: id,
        name: name,
      },
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    )
    .then((response) => {
      dispatch(updateUserRolesSuccess(response.data));
      dispatch(getRoles());
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
      dispatch(updateUserRolesFailed(error));
      console.log(error);
      toast.error("Update Failed. Please Try Again", {
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

export const deleteUserRolesSuccess = (data) => ({
  type: ActionTypes.DELETE_USER_ROLES_SUCCESS,
  payload: data,
});

export const deleteUserRolesProcessing = () => ({
  type: ActionTypes.DELETE_USER_ROLES_PROCESSING,
});

export const deleteUserRolesFailed = (error) => ({
  type: ActionTypes.DELETE_USER_ROLES_FAILED,
  payload: error,
});

export const deleteUserRoles = (id) => (dispatch) => {
  dispatch(deleteUserRolesProcessing());
  const token = localStorage.getItem("token");
  return axios
    .post(
      `${baseURL}api/users/roles/delete`,
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
      dispatch(deleteUserRolesSuccess(response));
      dispatch(getRoles());
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
      dispatch(deleteUserRolesFailed(error));
      console.log(error);
      toast.error("Please Try Again", {
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
