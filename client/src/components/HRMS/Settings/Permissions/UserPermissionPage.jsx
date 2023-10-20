import React, { useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import { useHistory, useParams } from "react-router-dom";
import { getSingleUser } from "../../../../redux/actions/usersAction";

import {
  getRoles,
  getSingleRole2,
} from "../../../../redux/actions/rolesActions";
import {
  Button,
  Card,
  CardBody,
  CardHeader,
  Container,
  FormGroup,
} from "reactstrap";
import {
  assignPermissionsToUser,
  getPermissions,
  changeUserRole,
} from "../../../../redux/actions/permissionActions";
import { Loader } from "../../../constants/loaders";
import { Formik, Form, Field } from "formik";
import Toast from "../../../constants/toast";
import { useTranslation } from "react-i18next";

const UserPermissionPage = (props) => {
  const userPermission = props.location.state;

  const { id } = useParams();
  const dispatch = useDispatch();
  const history = useHistory();

  const { t } = useTranslation();

  const [userName, setUserName] = useState("");
  const [userRole, setUserRole] = useState({});
  const [permissions, setPermissions] = useState([]);
  const [roles, setRoles] = useState([]);

  useEffect(() => {
    dispatch(getRoles());
  }, []);

  const rolesSelector = useSelector((state) => state.roles);

  useEffect(() => {
    if (rolesSelector.roles.data) {
      setRoles(rolesSelector.roles.data.Roles.data);
    }
  }, [rolesSelector.roles.data]);

  let settingPermissions = [1, 2, 3, 4, 5, 6, 7, 8, 9, 30];
  let displaySettingPermissions = [];
  let companyPermissions = [10, 11, 12, 29];
  let displayCompanyPermissions = [];
  let jobPermissions = [13, 14, 15, 16];
  let displayJobPermissions = [];
  let candidatePermissions = [17, 18, 19, 20, 21];
  let displayCandidatePermissions = [];
  let othersPermissions = [22, 23, 24, 25, 26, 27, 28];
  let displayOthersPermissions = [];

  useEffect(() => {
    const fetchSingleUser = async () => {
      try {
        const response = await dispatch(getSingleUser(id));
        setUserName(response.payload.data.user.name);
        response.payload.data.user.roles.map((role) => {
          setUserRole({ id: role.id, name: role.name });
        });
        if (response.payload.data.user.permissions) {
          let assignedPermissionsName = [];
          response.payload.data.user.permissions.map((items) => {
            assignedPermissionsName.push(items.name);
          });
          setFormValues({
            ...formValues,
            user_id: response.payload.data.user.user_pr_id,
            permissions: assignedPermissionsName,
          });
        }
      } catch (error) {
        throw error;
      }
    };
    fetchSingleUser();
  }, []);

  useEffect(() => {
    const fetchPermissions = async () => {
      try {
        const response = await dispatch(getPermissions(setPermissions));
      } catch (e) {
        throw e;
      }
    };
    fetchPermissions();
  }, []);

  const permissionSelector = useSelector((state) => state.permissions);
  const settingsSelector = useSelector((state) => state.settings);

  const initialValues = {
    user_id: id,
    permissions: [],
  };

  const [formValues, setFormValues] = useState(initialValues);

  function submitHandler(values) {
    let role_select = document.getElementById("role-select");
    dispatch(assignPermissionsToUser(values, history));
    if (role_select.value != userRole.id) {
      dispatch(changeUserRole({ user_pr_id: id, role_id: role_select.value }));
    }
  }

  if (!Array.isArray(permissions) && permissions.permissions.length > 0) {
    for (let i = 0; i < permissions.permissions.length; i++) {
      if (settingPermissions.includes(+permissions.permissions[i]["id"])) {
        displaySettingPermissions.push(permissions.permissions[i]);
      }
      if (companyPermissions.includes(+permissions.permissions[i]["id"])) {
        displayCompanyPermissions.push(permissions.permissions[i]);
      }
      if (jobPermissions.includes(+permissions.permissions[i]["id"])) {
        displayJobPermissions.push(permissions.permissions[i]);
      }
      if (candidatePermissions.includes(+permissions.permissions[i]["id"])) {
        displayCandidatePermissions.push(permissions.permissions[i]);
      }
      if (othersPermissions.includes(+permissions.permissions[i]["id"])) {
        displayOthersPermissions.push(permissions.permissions[i]);
      }
    }
  }

  function getRolePermissions(event) {
    let selected_role_id = event.target.value;
    dispatch(getSingleRole2(selected_role_id, id, setFormValues));
  }

  return (
    <Container fluid={true}>
      <div className={"w-100 text-right mt-2 mb-2"}>
        <Button
          type={"button"}
          color={"primary"}
          onClick={() => history.push("/jobportal-settings/4")}
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
          {t("buttons.back")}
        </Button>
      </div>
      <Card>
        <CardHeader>
          <h1>{t("settings.userPermissions.heading")}</h1>
        </CardHeader>
        <CardBody>
          <div>
            <h4
              style={{
                fontSize: `${
                  settingsSelector.FontSize === "Large"
                    ? "x-large"
                    : settingsSelector.FontSize === "Extra Large"
                    ? "xx-large"
                    : "1.5rem"
                }`,
              }}
            >
              {userName === undefined ? "" : userName} |{" "}
              {userRole === undefined ? "" : userRole.name}
            </h4>
          </div>
          <div>
            <div className="col-sm-2">
              <div className="form-group">
                <label htmlFor="exampleFormControlSelect1">
                  {t("settings.userPermissions.changeRole")}
                </label>
                <select
                  id="role-select"
                  className="form-control"
                  onChange={getRolePermissions}
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
                  {roles.map((role) => {
                    if (userRole.id == role.id) {
                      return (
                        <option key={role.id} selected value={role.id}>
                          {role.name}
                        </option>
                      );
                    } else {
                      return (
                        <option key={role.id} value={role.id}>
                          {role.name}
                        </option>
                      );
                    }
                  })}
                </select>
              </div>
            </div>
          </div>
          <div>
            <div className={"mt-5"}>
              <Formik
                enableReinitialize
                initialValues={formValues}
                onSubmit={submitHandler}
              >
                <Form>
                  <Field type={"hidden"} name={"user_id"} value={1} />
                  <FormGroup>
                    <div className={"row"}>
                      <div className="col-sm-2">
                        <div className={"permissions-columns-head"}>
                          <h5
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "x-large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "xx-large"
                                  : "1.25rem"
                              }`,
                            }}
                          >
                            Sätted
                          </h5>
                        </div>
                        <div className={"permissions-columns-permissions"}>
                          <ul className={"list-group"}>
                            {displaySettingPermissions.map((permissions) => {
                              return (
                                <li
                                  key={permissions.id}
                                  className={"list-group-item"}
                                >
                                  <Field
                                    type="checkbox"
                                    name="permissions"
                                    value={permissions.name}
                                  />{" "}
                                  {permissions.name}
                                </li>
                              );
                            })}
                          </ul>
                        </div>
                      </div>
                      <div className="col-sm-2">
                        <div className={"permissions-columns-head"}>
                          <h5
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "x-large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "xx-large"
                                  : "1.25rem"
                              }`,
                            }}
                          >
                            Ettevõtted
                          </h5>
                        </div>
                        <div className={"permissions-columns-permissions"}>
                          <ul className={"list-group"}>
                            {displayCompanyPermissions.map((permissions) => {
                              return (
                                <li
                                  key={permissions.id}
                                  className={"list-group-item"}
                                >
                                  <Field
                                    type="checkbox"
                                    name="permissions"
                                    value={permissions.name}
                                  />{" "}
                                  {permissions.name}
                                </li>
                              );
                            })}
                          </ul>
                        </div>
                      </div>
                      <div className="col-sm-2">
                        <div className={"permissions-columns-head"}>
                          <h5
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "x-large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "xx-large"
                                  : "1.25rem"
                              }`,
                            }}
                          >
                            Tööpakkumised
                          </h5>
                        </div>
                        <div className={"permissions-columns-permissions"}>
                          <ul className={"list-group"}>
                            {displayJobPermissions.map((permissions) => {
                              return (
                                <li
                                  key={permissions.id}
                                  className={"list-group-item"}
                                >
                                  <Field
                                    type="checkbox"
                                    name="permissions"
                                    value={permissions.name}
                                  />{" "}
                                  {permissions.name}
                                </li>
                              );
                            })}
                          </ul>
                        </div>
                      </div>
                      <div className="col-sm-2">
                        <div className={"permissions-columns-head"}>
                          <h5>Kandidaadid</h5>
                        </div>
                        <div className={"permissions-columns-permissions"}>
                          <ul className={"list-group"}>
                            {displayCandidatePermissions.map((permissions) => {
                              return (
                                <li
                                  key={permissions.id}
                                  className={"list-group-item"}
                                >
                                  <Field
                                    type="checkbox"
                                    name="permissions"
                                    value={permissions.name}
                                  />{" "}
                                  {permissions.name}
                                </li>
                              );
                            })}
                          </ul>
                        </div>
                      </div>
                      <div className="col-sm-2">
                        <div className={"permissions-columns-head"}>
                          <h5
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "x-large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "xx-large"
                                  : "1.25rem"
                              }`,
                            }}
                          >
                            Muu
                          </h5>
                        </div>
                        <div className={"permissions-columns-permissions"}>
                          <ul className={"list-group"}>
                            {displayOthersPermissions.map((permissions) => {
                              return (
                                <li
                                  key={permissions.id}
                                  className={"list-group-item"}
                                >
                                  <Field
                                    type="checkbox"
                                    name="permissions"
                                    value={permissions.name}
                                  />{" "}
                                  {permissions.name}
                                </li>
                              );
                            })}
                          </ul>
                        </div>
                      </div>
                    </div>
                  </FormGroup>
                  <FormGroup>
                    {userPermission &&
                    userPermission.includes("Can-Change-User-Permissions") ? (
                      <div className={"w-100 text-right"}>
                        <Button
                          type={"submit"}
                          color={"primary"}
                          disabled={permissionSelector.isLoading}
                        >
                          {permissionSelector.isLoading ? <Loader /> : "Save"}
                        </Button>
                      </div>
                    ) : null}
                  </FormGroup>
                </Form>
              </Formik>
            </div>
          </div>
        </CardBody>
      </Card>
      <Toast />
    </Container>
  );
};

export default UserPermissionPage;
