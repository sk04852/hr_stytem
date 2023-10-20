import * as ActionTypes from "../actionTypes";
import axios from "axios";
import { baseURL } from "../../components/Shared/baseURL";

export const getEducationDegreeSuccess = (data) => ({
  type: ActionTypes.GET_EDUCATION_DEGREE,
  payload: data,
});

export const getEducationDegree = () => (dispatch) => {
  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/education-degrees`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => dispatch(getEducationDegreeSuccess(response.data)));
};
