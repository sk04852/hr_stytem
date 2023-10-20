import * as ActionTypes from "../actionTypes";
import axios from "axios";
import { toast } from "react-toastify";
import { baseURL } from "../../components/Shared/baseURL";

export const getSkillsSuccess = (data) => ({
  type: ActionTypes.GET_SKILLS_SUCCESS,
  payload: data,
});

export const getSkills = () => (dispatch) => {
  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/open/skills`, {
      headers: { Authorization: `Bearer ${token}` },
    })
    .then((response) => dispatch(getSkillsSuccess(response)));
};

export const addSkillsSuccess = (data) => ({
  type: ActionTypes.ADD_SKILLS_SUCCESS,
  payload: data,
});

export const addSkillsProcessing = () => ({
  type: ActionTypes.ADD_SKILLS_PROCESSING,
});

export const addSkillsFailed = (error) => ({
  type: ActionTypes.ADD_SKILLS_FAILED,
  payload: error,
});

export const addSkills = (data) => (dispatch) => {
  dispatch(addSkillsProcessing());
  const token = localStorage.getItem("token");
  return axios
    .post(`${baseURL}api/open/candidate-skills`, data, {
      headers: { Authorization: `Bearer ${token}` },
    })
    .then((response) => {
      dispatch(addSkillsSuccess(response));
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
      dispatch(addSkillsFailed(error));
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
