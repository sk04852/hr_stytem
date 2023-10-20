import React, { useEffect, useState } from "react";
import {
  Button,
  Col,
  FormGroup,
  Label,
  Modal,
  ModalBody,
  ModalHeader,
  Row,
} from "reactstrap";
import { useDispatch, useSelector } from "react-redux";
import Toast from "../../constants/toast";
import { toast } from "react-toastify";
import { Formik, Form, Field } from "formik";
import {
  addNewUser,
  deleteUser,
  getAllUsers,
} from "../../../redux/actions/usersAction";
import { Loader, DataLoader, ActionsLoader } from "../../constants/loaders";
import asterik from "../../../assets/images/asterisk.png";
import AvatarImage from "../../../assets/images/avatar.jpg";
import {
  FormValidationErrors,
  SubmitFormValidationErrors,
} from "../../constants/errors";
import { useTranslation } from "react-i18next";
import ZoomIcon from "../../../assets/images/zoom-icon.png";
import axios from "axios";
import { baseURL } from "../../Shared/baseURL";
import { UserSchema } from "./validations/formValidations";
import { validateHtmlTags } from "./validations/validateFuncations";

const Users = ({ permissions }) => {
  const initialValues = {
    name: "",
    job_title: "",
    location: "",
    role_id: "",
    email: "",
    phone: "",
    password: "",
    password_confirmation: "",
    timezone: "",
  };

  const dispatch = useDispatch();

  const { t } = useTranslation();

  const [modal, setModal] = useState(false);
  const [formValues, setFormValues] = useState(initialValues);
  const [userRoles, setUserRoles] = useState([]);
  const [deleteID, setDeleteID] = useState([]);
  const [usersData, setUsersData] = useState("");
  const [errors, setErrors] = useState({});
  const [isLoading, setIsLoading] = useState(false);
  const [formErrors, setFormErrors] = useState({});

  const toggleModal = () => {
    setModal(!modal);
  };

  const getAllUsersSelector = useSelector((state) => state.users.data.data);
  const rolesSelector = useSelector((state) => state.roles.roles.data);
  const usersLoadingSelector = useSelector((state) => state.users.isLoading);
  const settingsSelector = useSelector((state) => state.settings);

  useEffect(() => {
    if (rolesSelector) {
      setUserRoles(rolesSelector.Roles.data);
    }
  }, [rolesSelector]);

  const fetchAllUsers = async () => {
    try {
      const response = await dispatch(getAllUsers());
    } catch (error) {
      throw error;
    }
  };

  useEffect(() => {
    fetchAllUsers();
  }, []);

  useEffect(() => {
    if (getAllUsersSelector) {
      setUsersData(getAllUsersSelector.Users.data);
    }
  }, [getAllUsersSelector]);

  const handleSubmit = (values) => {
    // validate
    let error = {};
    error = validateHtmlTags(values, setFormErrors);

    // submit form if there is no error
    if (Object.keys(error).length === 0) {
      dispatch(addNewUser(values, setModal, setErrors));
    }
  };

  const renderAddNewUserModal = () => {
    return (
      <>
        <Modal isOpen={modal} toggle={toggleModal}>
          <ModalHeader
            toggle={toggleModal}
            style={{
              fontSize: `${
                settingsSelector.FontSize === "Large"
                  ? "large"
                  : settingsSelector.FontSize === "Extra Large"
                  ? "x-large"
                  : "1.25rem"
              }`,
            }}
            className={"mb-2"}
          >
            {t("settings.users.addNewUser")}
          </ModalHeader>

          {/*ERRORS*/}
          {errors && Object.keys(errors).length !== 0 ? (
            <FormValidationErrors errors={errors} />
          ) : null}

          {/*FORM INPUT ERRORS*/}
          {formErrors && Object.keys(formErrors).length !== 0 ? (
            <SubmitFormValidationErrors formErrors={formErrors} />
          ) : null}

          <ModalBody>
            <Formik
              initialValues={formValues}
              validationSchema={UserSchema}
              onSubmit={(values) => handleSubmit(values)}
            >
              {({ errors, touched }) => (
                <Form id={"add-user-form"}>
                  <fieldset disabled={usersLoadingSelector}>
                    <Row>
                      <Col sm={6}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("settings.users.name")}{" "}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Field
                            type="text"
                            placeholder="Nimi"
                            className={"form-control"}
                            id={"name"}
                            name={"name"}
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          />
                          {errors.name && touched.name ? (
                            <div style={{ color: "red" }}>{errors.name}</div>
                          ) : null}
                        </FormGroup>
                      </Col>
                      <Col sm={6}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("settings.users.position")}
                          </Label>
                          <Field
                            type="text"
                            placeholder="Amet"
                            className={"form-control"}
                            id={"job_title"}
                            name={"job_title"}
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          />
                          {errors.job_title && touched.job_title ? (
                            <div style={{ color: "red" }}>
                              {errors.job_title}
                            </div>
                          ) : null}
                        </FormGroup>
                      </Col>
                      <Col sm={6}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("settings.users.location")}
                          </Label>
                          <Field
                            type="text"
                            placeholder="Aadress"
                            className={"form-control"}
                            id={"location"}
                            name={"location"}
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          />
                          {errors.location && touched.location ? (
                            <div style={{ color: "red" }}>
                              {errors.location}
                            </div>
                          ) : null}
                        </FormGroup>
                      </Col>
                      <Col sm={6}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("settings.users.phone")}
                          </Label>
                          <Field
                            type="number"
                            placeholder="Telefon"
                            className={"form-control"}
                            id={"phone"}
                            name={"phone"}
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          />
                          {errors.phone && touched.phone ? (
                            <div style={{ color: "red" }}>{errors.phone}</div>
                          ) : null}
                        </FormGroup>
                      </Col>
                      <Col sm={6}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("settings.users.email")}{" "}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Field
                            type="text"
                            placeholder="Email"
                            className={"form-control"}
                            id={"email"}
                            name={"email"}
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          />
                          {errors.email && touched.email ? (
                            <div style={{ color: "red" }}>{errors.email}</div>
                          ) : null}
                        </FormGroup>
                      </Col>
                      <Col sm={6}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("settings.users.role")}{" "}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Field
                            as={"select"}
                            className="custom-select"
                            name={"role_id"}
                            id={"role_id"}
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          >
                            <option value={""}>Ei ole valitud</option>
                            {userRoles.map((items, index) => (
                              <option value={items.id} key={index}>
                                {items.name}
                              </option>
                            ))}
                          </Field>
                          {errors.role_id && touched.role_id ? (
                            <div style={{ color: "red" }}>{errors.role_id}</div>
                          ) : null}
                        </FormGroup>
                      </Col>
                      <Col sm={6}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("settings.users.password")}{" "}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Field
                            type="password"
                            placeholder="Uus parool"
                            className={"form-control"}
                            id={"password"}
                            name={"password"}
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          />
                          {errors.password && touched.password ? (
                            <div style={{ color: "red" }}>
                              {errors.password}
                            </div>
                          ) : null}
                        </FormGroup>
                      </Col>
                      <Col sm={6}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("settings.users.confirmPassword")}{" "}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Field
                            type="password"
                            placeholder="Kinnita uus parool"
                            className={"form-control"}
                            id={"password_confirmation"}
                            name={"password_confirmation"}
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          />
                          {errors.password_confirmation &&
                          touched.password_confirmation ? (
                            <div style={{ color: "red" }}>
                              {errors.password_confirmation}
                            </div>
                          ) : null}
                        </FormGroup>
                      </Col>
                      <FormGroup className={"w-100 text-right"}>
                        <Button
                          type={"submit"}
                          color={"primary"}
                          className={"mr-2"}
                          disabled={usersLoadingSelector}
                          style={{
                            fontSize: `${
                              settingsSelector.FontSize === "Large"
                                ? "large"
                                : settingsSelector.FontSize === "Extra Large"
                                ? "x-large"
                                : "14px"
                            }`,
                          }}
                        >
                          {usersLoadingSelector ? (
                            <Loader />
                          ) : (
                            <>{t("buttons.create")}</>
                          )}
                        </Button>
                        <Button
                          type={"button"}
                          onClick={() => toggleModal()}
                          style={{
                            fontSize: `${
                              settingsSelector.FontSize === "Large"
                                ? "large"
                                : settingsSelector.FontSize === "Extra Large"
                                ? "x-large"
                                : "14px"
                            }`,
                          }}
                        >
                          {t("buttons.close")}
                        </Button>
                      </FormGroup>
                    </Row>
                  </fieldset>
                </Form>
              )}
            </Formik>
          </ModalBody>
        </Modal>
      </>
    );
  };

  const handleDelete = (e, id, userpr_id) => {
    deleteID.push({
      id: id,
      userpr_id: userpr_id,
    });

    const confirm = window.confirm("Are You Sure?");
    if (confirm === true) {
      dispatch(deleteUser(deleteID, setDeleteID, setModal));
    }
  };

  const handleZoomLink = async (id) => {
    let token = localStorage.getItem("token");
    setIsLoading(true);
    return axios
      .get(`${baseURL}api/users/link-zoom-account/${id}`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
      .then((response) => {
        setIsLoading(false);
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
        setIsLoading(false);
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

  return (
    <>
      {renderAddNewUserModal()}
      <div className={"w-100"}>
        <div className="tab-content mt-3">
          <h3
            style={{
              fontSize: `${
                settingsSelector.FontSize === "Large"
                  ? "2rem"
                  : settingsSelector.FontSize === "Extra Large"
                  ? "2.50rem"
                  : "1.75rem"
              }`,
            }}
          >
            {t("settings.users.heading")}
          </h3>
          <div className={"w-100 text-right mb-3"}>
            {permissions.includes("Can-Add-User") ? (
              <Button
                type={"button"}
                color={"primary"}
                onClick={() => setModal(true)}
                style={{
                  fontSize: `${
                    settingsSelector.FontSize === "Large"
                      ? "large"
                      : settingsSelector.FontSize === "Extra Large"
                      ? "x-large"
                      : "14px"
                  }`,
                }}
              >
                {t("settings.users.addNewUser")}
              </Button>
            ) : null}
          </div>
          <div className="">
            <div className="table-responsive">
              {/* {deleteID.length > 2 ? (
                <Button
                  type={"button"}
                  color={"danger"}
                  onClick={(e) => handleDelete(deleteID)}
                >
                  Kustuta
                </Button>
              ) : (
                ""
              )} */}
              <table className="table table-striped table-hover table-vcenter text-nowrap mb-0">
                <thead>
                  <tr>
                    {/*<th>#</th>*/}
                    <th
                      style={{
                        fontSize: `${
                          settingsSelector.FontSize === "Large"
                            ? "large"
                            : settingsSelector.FontSize === "Extra Large"
                            ? "x-large"
                            : "14px"
                        }`,
                      }}
                    >
                      ID
                    </th>
                    <th
                      style={{
                        fontSize: `${
                          settingsSelector.FontSize === "Large"
                            ? "large"
                            : settingsSelector.FontSize === "Extra Large"
                            ? "x-large"
                            : "14px"
                        }`,
                      }}
                    >
                      {t("settings.users.picture")}
                    </th>
                    <th
                      style={{
                        fontSize: `${
                          settingsSelector.FontSize === "Large"
                            ? "large"
                            : settingsSelector.FontSize === "Extra Large"
                            ? "x-large"
                            : "14px"
                        }`,
                      }}
                    >
                      {t("settings.users.name")}
                    </th>
                    <th
                      style={{
                        fontSize: `${
                          settingsSelector.FontSize === "Large"
                            ? "large"
                            : settingsSelector.FontSize === "Extra Large"
                            ? "x-large"
                            : "14px"
                        }`,
                      }}
                    >
                      {t("settings.users.role")}
                    </th>
                    <th
                      className="w100"
                      style={{
                        fontSize: `${
                          settingsSelector.FontSize === "Large"
                            ? "large"
                            : settingsSelector.FontSize === "Extra Large"
                            ? "x-large"
                            : "14px"
                        }`,
                      }}
                    >
                      {t("actions")}
                    </th>
                  </tr>
                </thead>
                <tbody className={"w-100"}>
                  {usersData ? (
                    usersData.reverse().map((items, index) => (
                      <tr key={index}>
                        <td
                          style={{
                            fontSize: `${
                              settingsSelector.FontSize === "Large"
                                ? "large"
                                : settingsSelector.FontSize === "Extra Large"
                                ? "x-large"
                                : "14px"
                            }`,
                          }}
                        >
                          {index + 1}
                          {/*{items.id}*/}
                        </td>
                        <td
                          style={{
                            fontSize: `${
                              settingsSelector.FontSize === "Large"
                                ? "large"
                                : settingsSelector.FontSize === "Extra Large"
                                ? "x-large"
                                : "14px"
                            }`,
                          }}
                        >
                          <span
                            className="avatar avatar-blue"
                            data-toggle="tooltip"
                            data-placement="top"
                            data-original-title="Avatar Name"
                          >
                            <img
                              src={items.photo ? items.photo : AvatarImage}
                              alt="image"
                            />
                          </span>
                        </td>
                        <td
                          style={{
                            fontSize: `${
                              settingsSelector.FontSize === "Large"
                                ? "large"
                                : settingsSelector.FontSize === "Extra Large"
                                ? "x-large"
                                : "14px"
                            }`,
                          }}
                        >
                          {items.name}
                        </td>
                        {items.roles.map((userRoles, index) => (
                          <td
                            key={index}
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          >
                            {userRoles.name}
                          </td>
                        ))}
                        <td>
                          <button
                            type="button"
                            className="btn btn-icon js-sweetalert"
                            title="Delete"
                            data-type="confirm"
                            onClick={() => handleZoomLink(items.userpr_id)}
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          >
                            {isLoading ? (
                              <ActionsLoader />
                            ) : (
                              <img src={ZoomIcon} width={20} height={20} />
                            )}
                          </button>
                          {permissions.includes("Can-Delete-User") ? (
                            <button
                              type="button"
                              className="btn btn-icon js-sweetalert"
                              title="Delete"
                              data-type="confirm"
                              onClick={(e) =>
                                handleDelete(e, items.id, items.userpr_id)
                              }
                              style={{
                                fontSize: `${
                                  settingsSelector.FontSize === "Large"
                                    ? "large"
                                    : settingsSelector.FontSize ===
                                      "Extra Large"
                                    ? "x-large"
                                    : "14px"
                                }`,
                              }}
                            >
                              <i className="fa fa-trash-o text-danger" />
                            </button>
                          ) : null}
                        </td>
                      </tr>
                    ))
                  ) : (
                    <tr>
                      <td>
                        <DataLoader />
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <Toast />
    </>
  );
};

export default Users;
