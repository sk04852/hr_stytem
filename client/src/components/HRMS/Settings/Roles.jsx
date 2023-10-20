import React, { useState } from "react";
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
import { DataLoader, Loader } from "../../constants/loaders";
import Toast from "../../constants/toast";
import { Field, Form, Formik } from "formik";
import { useDispatch, useSelector } from "react-redux";
import {
  deleteUserRoles,
  postUserRoles,
  updateUserRoles,
} from "../../../redux/actions/rolesActions";
import { useHistory } from "react-router-dom";
import asterik from "../../../assets/images/asterisk.png";
import { NoRecordFound } from "../../constants/noRecordFound";
import { useTranslation } from "react-i18next";
import { UserRolesSchema } from "./validations/formValidations";
import { SubmitFormValidationErrors } from "../../constants/errors";
import { validateHtmlTags } from "./validations/validateFuncations";

const Roles = (props) => {
  const { userRoles } = props;

  const dispatch = useDispatch();
  const history = useHistory();

  const { t } = useTranslation();

  const initialValues = {
    name: "",
  };

  const [modal, setModal] = useState(false);
  const [editable, setEditable] = useState(false);
  const [editValues, setEditValues] = useState(initialValues);
  const [editId, setEditId] = useState("");
  const [formErrors, setFormErrors] = useState({});

  const toggleModal = () => {
    setModal(!modal);
  };

  const userRolesSelector = useSelector((state) => state.roles);
  const settingsSelector = useSelector((state) => state.settings);

  const handleSubmit = (values) => {
    // validate
    let error = {};
    error = validateHtmlTags(values, setFormErrors);

    // submit form if there is no error
    if (Object.keys(error).length === 0) {
      dispatch(postUserRoles(values));
    }
  };

  const handleUpdate = (values) => {
    // validate
    let error = {};
    error = validateHtmlTags(values, setFormErrors);

    // submit form if there is no error
    if (Object.keys(error).length === 0) {
      dispatch(updateUserRoles(editId, values.name));
    }
  };

  const handleDelete = (id) => {
    const confirm = window.confirm("Oled kindel?");
    if (confirm === true) {
      dispatch(deleteUserRoles(id));
    }
  };

  const renderRoleModal = () => {
    return (
      <>
        {!editable ? (
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
              {t("settings.roles.addNewRole")}
            </ModalHeader>

            {/*FORM INPUT ERRORS*/}
            {formErrors && Object.keys(formErrors).length !== 0 ? (
              <SubmitFormValidationErrors formErrors={formErrors} />
            ) : null}

            <ModalBody>
              <Formik
                initialValues={initialValues}
                validationSchema={UserRolesSchema}
                onSubmit={(values) => handleSubmit(values)}
              >
                {({ errors, touched }) => (
                  <Form>
                    <fieldset disabled={userRolesSelector.isLoading}>
                      <Row>
                        <Col sm={12}>
                          <FormGroup>
                            <Label
                              style={{
                                fontSize: `${
                                  settingsSelector.FontSize === "Large"
                                    ? "large"
                                    : settingsSelector.FontSize ===
                                      "Extra Large"
                                    ? "x-large"
                                    : "0.875rem"
                                }`,
                              }}
                            >
                              {t("settings.roles.roleName")}{" "}
                              <img
                                src={asterik}
                                height={10}
                                width={10}
                                className="mt-n2"
                              />
                            </Label>
                            <Field
                              type="text"
                              placeholder="Roll"
                              className={"form-control"}
                              id={"name"}
                              name={"name"}
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
                            />
                            {errors.name && touched.name ? (
                              <div style={{ color: "red" }}>{errors.name}</div>
                            ) : null}
                          </FormGroup>
                        </Col>
                        <FormGroup className={"w-100 text-right"}>
                          <Button
                            type={"submit"}
                            color={"primary"}
                            className={"mr-2"}
                            disabled={userRolesSelector.isLoading}
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
                            {userRolesSelector.isLoading ? (
                              <Loader />
                            ) : (
                              <>{t("settings.roles.addRoleButton")}</>
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
        ) : (
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
              {t("settings.roles.editRole")}
            </ModalHeader>

            {/*FORM INPUT ERRORS*/}
            {formErrors && Object.keys(formErrors).length !== 0 ? (
              <SubmitFormValidationErrors formErrors={formErrors} />
            ) : null}

            <ModalBody>
              <Formik
                initialValues={editValues}
                validationSchema={UserRolesSchema}
                onSubmit={(values) => handleUpdate(values)}
              >
                {({ errors, touched }) => (
                  <Form>
                    <fieldset disabled={userRolesSelector.isLoading}>
                      <Row>
                        <Col sm={12}>
                          <FormGroup>
                            <Label
                              style={{
                                fontSize: `${
                                  settingsSelector.FontSize === "Large"
                                    ? "large"
                                    : settingsSelector.FontSize ===
                                      "Extra Large"
                                    ? "x-large"
                                    : "0.875rem"
                                }`,
                              }}
                            >
                              {t("settings.roles.roleName")}{" "}
                              <img
                                src={asterik}
                                height={10}
                                width={10}
                                className="mt-n2"
                              />
                            </Label>
                            <Field
                              type="text"
                              placeholder="Role"
                              className={"form-control"}
                              id={"name"}
                              name={"name"}
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
                            />
                            {errors.name && touched.name ? (
                              <div style={{ color: "red" }}>{errors.name}</div>
                            ) : null}
                          </FormGroup>
                        </Col>
                        <FormGroup className={"w-100 text-right"}>
                          <Button
                            type={"submit"}
                            color={"primary"}
                            className={"mr-2"}
                            disabled={userRolesSelector.isLoading}
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
                            {userRolesSelector.isLoading ? (
                              <Loader />
                            ) : (
                              <>{t("settings.roles.updateRoleButton")}</>
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
        )}
      </>
    );
  };

  return (
    <>
      {renderRoleModal()}
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
            {t("settings.roles.heading")}
          </h3>
          <div className={"w-100 text-right mb-3"}>
            <Button
              type={"button"}
              color={"primary"}
              onClick={() => {
                setModal(true);
                setEditable(false);
              }}
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
              {t("settings.roles.addRoleButton")}
            </Button>
          </div>
          <div className="">
            <div className="table-responsive">
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
                      {t("settings.roles.roleName")}
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
                  {!userRolesSelector.isLoading ? (
                    <>
                      {userRoles ? (
                        userRoles.map((roles, index) => (
                          <tr key={index}>
                            <td
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
                              {roles.id}
                            </td>
                            <td
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
                              {roles.name}
                            </td>
                            <td
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
                              <button
                                type="button"
                                className="btn btn-icon js-sweetalert"
                                title="Permissions"
                                // onClick={(e) => handleDelete(roles.id)}
                                onClick={() =>
                                  history.push(
                                    `/jobportal-settings/role-permissions/${roles.id}/permissions`
                                  )
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
                                <i className="fa fa-unlock-alt"></i>
                              </button>
                              <button
                                type="button"
                                className="btn btn-icon"
                                title="Edit"
                                onClick={() => {
                                  setEditId(roles.id);
                                  const getRoleById = userRoles.find((item) => {
                                    if (item.id === roles.id) {
                                      setEditValues({
                                        ...editValues,
                                        name: roles.name,
                                      });
                                    }
                                  });
                                  setEditable(true);
                                  setModal(true);
                                }}
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
                                <i className="fa fa-edit" />
                              </button>
                              <button
                                type="button"
                                className="btn btn-icon js-sweetalert"
                                title="Delete"
                                data-type="confirm"
                                onClick={(e) => handleDelete(roles.id)}
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
                            </td>
                          </tr>
                        ))
                      ) : (
                        <tr>
                          <td>
                            <NoRecordFound />
                          </td>
                        </tr>
                      )}
                    </>
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

export default Roles;
