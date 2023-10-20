import * as ActionTypes from "../actionTypes";
import axios from "axios";
import { baseURL } from "../../components/Shared/baseURL";

export const getCandidateActionsSuccess = (data) => ({
  type: ActionTypes.GET_ACTIONS_SUCCESS,
  payload: data,
});

export const getCandidateActionsProcessing = () => ({
  type: ActionTypes.GET_ACTIONS_PROCESSING,
});

export const getCandidateActionsFailed = (error) => ({
  type: ActionTypes.GET_ACTIONS_FAILED,
  payload: error,
});

export const getCandidateActions = () => (dispatch) => {
  dispatch(getCandidateActionsProcessing());
  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/open/actions?sort=ASC`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => dispatch(getCandidateActionsSuccess(response.data)))
    .catch((error) => dispatch(getCandidateActionsFailed(error.response.data)));
};
