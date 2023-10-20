import * as ActionTypes from "../actionTypes";

export const getJobTypeSuccess = (data) => ({
  type: ActionTypes.GET_JOB_TYPES,
  payload: data,
});

export const getJobType = () => (dispatch) => {
  return axios
    .get("")
    .then((response) => dispatch(getJobTypeSuccess(response)));
};
