import * as ActionTypes from "../actionTypes";
import axios from "axios";
import { toast } from "react-toastify";
import { baseURL } from "../../components/Shared/baseURL";
import {
  DELETE_CANDIDATE_LANGUAGE_FAILED,
  DELETE_CANDIDATE_LANGUAGE_PROCESSING,
  DELETE_CANDIDATE_LANGUAGE_SUCCESS,
} from "../actionTypes";

export const getLanguagesSuccess = (data) => ({
  type: ActionTypes.GET_LANGUAGES_SUCCESS,
  payload: data,
});

export const getLanguages = () => (dispatch) => {
  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/languages`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => dispatch(getLanguagesSuccess(response.data)));
};

export const getLanguagesByIdSuccess = (data) => ({
  type: ActionTypes.GET_CANDIDATE_LANGUAGE_BY_ID_SUCCESS,
  payload: data,
});

export const getLanguageById = (id) => (dispatch) => {
  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/open/candidate-languages/` + id, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => {
      dispatch(getLanguagesByIdSuccess(response.data));
    });
};

export const addCandidateLanguageSuccess = (data) => ({
  type: ActionTypes.ADD_CANDIDATE_LANGUAGE_SUCCESS,
  payload: data,
});

export const addCandidateLanguageProcessing = () => ({
  type: ActionTypes.ADD_CANDIDATE_LANGUAGE_PROCESSING,
});

export const addCandidateLanguageFailed = (error) => ({
  type: ActionTypes.ADD_CANDIDATE_LANGUAGE_FAILED,
  payload: error,
});

export const addCandidateLanguage =
  (formData, setErrors, setTabsId) => (dispatch) => {
    dispatch(addCandidateLanguageProcessing());
    const token = localStorage.getItem("token");
    return axios
      .post(`${baseURL}api/open/candidate-languages`, formData, {
        headers: { Authorization: `Bearer ${token}` },
      })
      .then((response) => {
        dispatch(addCandidateLanguageSuccess(response.data));
        toast.success(response.data.message, {
          position: "top-right",
          autoClose: 3000,
          hideProgressBar: true,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true,
          progress: undefined,
        });
        setErrors({});
        setTabsId("6");
      })
      .catch((error) => {
        if (error.response !== undefined) {
          dispatch(addCandidateLanguageFailed(error.response.data));
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
        }
      });
  };

export const addCandidateLanguageManuallySuccess = (data) => ({
  type: ActionTypes.ADD_CANDIDATE_LANGUAGE_MANUALLY_SUCCESS,
  payload: data,
});

export const addCandidateLanguageManuallyProcessing = () => ({
  type: ActionTypes.ADD_CANDIDATE_LANGUAGE_MANUALLY_PROCESSING,
});

export const addCandidateLanguageManuallyFailed = (error) => ({
  type: ActionTypes.ADD_CANDIDATE_LANGUAGE_MANUALLY_FAILED,
  payload: error,
});

export const addCandidateLanguageManually = (values) => (dispatch) => {
  dispatch(addCandidateLanguageManuallyProcessing());
  const token = localStorage.getItem("token");
  return axios
    .post(
      `${baseURL}api/open/languages`,
      {
        candidatecv_id: values.candidatecv_id,
        name: values.name,
        level: values.level,
      },
      {
        headers: { Authorization: `Bearer ${token}` },
      }
    )
    .then((response) => {
      dispatch(addCandidateLanguageManuallySuccess(response));
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
      dispatch(addCandidateLanguageManuallyFailed(error));
      toast.error("Language Added Failed", {
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

export const updateCandidateLanguageSuccess = (data) => ({
  type: ActionTypes.UPDATE_CANDIDATE_LANGUAGE_SUCCESS,
  payload: data,
});

export const updateCandidateLanguageProcessing = () => ({
  type: ActionTypes.UPDATE_CANDIDATE_LANGUAGE_PROCESSING,
});

export const updateCandidateLanguageFailed = (error) => ({
  type: ActionTypes.UPDATE_CANDIDATE_LANGUAGE_FAILED,
  payload: error,
});

export const updateCandidateLanguage = (formData, setErrors) => (dispatch) => {
  dispatch(updateCandidateLanguageProcessing());
  const token = localStorage.getItem("token");
  return axios
    .post(`${baseURL}api/open/candidate-languages/update`, formData, {
      headers: { Authorization: `Bearer ${token}` },
    })
    .then((response) => {
      dispatch(updateCandidateLanguageSuccess(response.data));
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
        dispatch(updateCandidateLanguageFailed(error.response.data));
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
      }
    });
};

export const deleteCandidateLanguageSuccess = (data) => ({
  type: ActionTypes.DELETE_CANDIDATE_LANGUAGE_SUCCESS,
  payload: data,
});

export const deleteCandidateLanguageProcessing = () => ({
  type: ActionTypes.DELETE_CANDIDATE_LANGUAGE_PROCESSING,
});

export const deleteCandidateLanguageFailed = (error) => ({
  type: ActionTypes.DELETE_CANDIDATE_LANGUAGE_FAILED,
  payload: error,
});

export const deleteCandidateLanguage =
  (id, languagesIDs, setErrors) => (dispatch) => {
    dispatch(deleteCandidateLanguageProcessing());
    const token = localStorage.getItem("token");
    return axios
      .post(
        `${baseURL}api/open/candidate-languages/delete`,
        {
          candidatecv_id: id,
          language_ids: languagesIDs,
        },
        {
          headers: { Authorization: `Bearer ${token}` },
        }
      )
      .then((response) => {
        dispatch(deleteCandidateLanguageSuccess(response.data));
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
          dispatch(deleteCandidateLanguageFailed(error.response.data));
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
        }
      });
  };
