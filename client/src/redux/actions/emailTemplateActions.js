import * as ActionTypes from "../actionTypes";
import axios from "axios";
import { baseURL } from "../../components/Shared/baseURL";
import { toast } from "react-toastify";

export const getEmailTemplatesSuccess = (data) => ({
  type: ActionTypes.GET_EMAIL_TEMPLATES_SUCCESS,
  payload: data,
});

export const getEmailTemplatesProcessing = () => ({
  type: ActionTypes.GET_EMAIL_TEMPLATES_PROCESSING,
});

export const getEmailTemplatesFailed = (error) => ({
  type: ActionTypes.GET_EMAIL_TEMPLATES_FAILED,
  payload: error,
});

export const getEmailTemplates = (pageNumber, perPage = 10) => (dispatch) => {
  dispatch(getEmailTemplatesProcessing());

  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/mail/templates?page=${pageNumber}&per_page=${perPage}`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => dispatch(getEmailTemplatesSuccess(response.data)))
    .catch((error) => {
      if (error.response !== undefined) {
        dispatch(getEmailTemplatesFailed(error.response.data));
      }
    });
};

export const getEmailTemplateByIdSuccess = (data) => ({
  type: ActionTypes.GET_EMAIL_TEMPLATE_BY_ID_SUCCESS,
  payload: data,
});

export const getEmailTemplateByIdProcessing = () => ({
  type: ActionTypes.GET_EMAIL_TEMPLATE_BY_ID_PROCESSING,
});

export const getEmailTemplateByIdFailed = (error) => ({
  type: ActionTypes.GET_EMAIL_TEMPLATE_BY_ID_FAILED,
  payload: error,
});

export const getEmailTemplateById = (id) => (dispatch) => {
  dispatch(getEmailTemplateByIdProcessing());

  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/mail/templates/${id}`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => dispatch(getEmailTemplateByIdSuccess(response.data)))
    .catch((error) => dispatch(getEmailTemplateByIdFailed(error)));
};

export const addEmailTemplatesSuccess = (data) => ({
  type: ActionTypes.ADD_EMAIL_TEMPLATES_SUCCESS,
  payload: data,
});

export const addEmailTemplatesProcessing = () => ({
  type: ActionTypes.ADD_EMAIL_TEMPLATES_PROCESSING,
});

export const addEmailTemplatesFailed = (error) => ({
  type: ActionTypes.ADD_EMAIL_TEMPLATES_FAILED,
  payload: error,
});

export const addEmailTemplates =
  (values, setTemplateKeyErrorMsg) => (dispatch) => {
    dispatch(addEmailTemplatesProcessing());

    const token = localStorage.getItem("token");
    return axios
      .post(`${baseURL}api/mail/templates`, values, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
      .then((response) => {
        dispatch(addEmailTemplatesSuccess(response));
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
        dispatch(addEmailTemplatesFailed(error));
        setTemplateKeyErrorMsg(error.response.data.errors.template_key[0]);
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

export const updateEmailTemplatesSuccess = (data) => ({
  type: ActionTypes.UPDATE_EMAIL_TEMPLATES_SUCCESS,
  payload: data,
});

export const updateEmailTemplatesProcessing = () => ({
  type: ActionTypes.UPDATE_EMAIL_TEMPLATES_PROCESSING,
});

export const updateEmailTemplatesFailed = (error) => ({
  type: ActionTypes.UPDATE_EMAIL_TEMPLATES_FAILED,
  payload: error,
});

export const updateEmailTemplates = (id, values) => (dispatch) => {
  dispatch(updateEmailTemplatesProcessing());

  const token = localStorage.getItem("token");
  return axios
    .post(
      `${baseURL}api/mail/templates/update`,
      {
        id: id,
        title: values.title,
        template_key: values.template_key,
        subject: values.subject,
        to: values.to,
        cc: values.cc,
        body: values.body,
      },
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    )
    .then((response) => {
      dispatch(updateEmailTemplatesSuccess(response));
      toast.success(response.data.message, {
        position: "top-right",
        autoClose: 3000,
        hideProgressBar: true,
        closeOnClick: true,
        pauseOnHover: true,
        draggable: true,
        progress: undefined,
      });
      // dispatch(getEmailTemplates(1));
    })
    .catch((error) => {
      dispatch(updateEmailTemplatesFailed(error));
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

export const deleteEmailTemplatesSuccess = (data) => ({
  type: ActionTypes.DELETE_EMAIL_TEMPLATES_SUCCESS,
  payload: data,
});

export const deleteEmailTemplatesProcessing = () => ({
  type: ActionTypes.DELETE_EMAIL_TEMPLATES_PROCESSING,
});

export const deleteEmailTemplatesFailed = (error) => ({
  type: ActionTypes.DELETE_EMAIL_TEMPLATES_FAILED,
  payload: error,
});

export const deleteEmailTemplates = (id) => (dispatch) => {
  dispatch(deleteEmailTemplatesProcessing());

  const token = localStorage.getItem("token");
  return axios
    .post(
      `${baseURL}api/mail/templates/delete`,
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
      dispatch(deleteEmailTemplatesSuccess(response));
      toast.success(response.data.message, {
        position: "top-right",
        autoClose: 3000,
        hideProgressBar: true,
        closeOnClick: true,
        pauseOnHover: true,
        draggable: true,
        progress: undefined,
      });
      dispatch(getEmailTemplates(1));
    })
    .catch((error) => {
      dispatch(deleteEmailTemplatesFailed(error));
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

//----------------------- WILDCARDS ACTIONS -----------------//
export const getWildcardsSuccess = (data) => ({
  type: ActionTypes.GET_WILDCARDS_SUCCESS,
  payload: data,
});

export const getWildcardsProcessing = () => ({
  type: ActionTypes.GET_WILDCARDS_PROCESSING,
});

export const getWildcardsFailed = (error) => ({
  type: ActionTypes.GET_WILDCARDS_FAILED,
  payload: error,
});

export const getWildcards = () => (dispatch) => {
  dispatch(getWildcardsProcessing());

  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/mail/wildcards`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => dispatch(getWildcardsSuccess(response)))
    .catch((error) => dispatch(getWildcardsFailed(error)));
};
