import * as ActionTypes from "../actionTypes";
import axios from "axios";
import {baseURL} from "../../components/Shared/baseURL";

export const getTimezoneSuccess = (data) => ({
  type: ActionTypes.GET_TIMEZONE,
  payload: data,
});

export const getTimezone = () => (dispatch) => {
  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/timezones`, {
      headers: { Authorization: `Bearer ${token}` },
    })
    .then((response) => dispatch(getTimezoneSuccess(response)));
};
