import React, { useEffect, useState } from "react";
import {
  Button,
  Card,
  CardBody,
  CardHeader,
  Container,
  FormGroup,
} from "reactstrap";
import { useDispatch, useSelector } from "react-redux";
import { getSingleRole } from "../../../redux/actions/rolesActions";
import { useHistory, useParams } from "react-router-dom";
import {
  assignPermissionsToRole,
  getPermissions,
} from "../../../redux/actions/permissionActions";
import { Loader } from "../../constants/loaders";
import { Field, Form, Formik } from "formik";
import Toast from "../../constants/toast";
import { useTranslation } from "react-i18next";

const RolePermissions = () => {
  const { id } = useParams();
  const dispatch = useDispatch();
  const history = useHistory();

  const { t } = useTranslation();

  const [roleName, setRoleName] = useState("");
  const [userPermissions, setUserPermissions] = useState([]);
  const [formValues, setFormValues] = useState({
    role_id: "",
    permissions: [],
  });

  useEffect(() => {
    dispatch(getSingleRole(id, setRoleName, setFormValues));
    dispatch(getPermissions(setUserPermissions));
  }, []);

  const permissionSelector = useSelector((state) => state.permissions);

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

  function submitHandler(values) {
    dispatch(assignPermissionsToRole(values, history));
  }

  let permissions = permissionSelector.isLoading
    ? []
    : permissionSelector.data.permissions;
  if (Array.isArray(permissions) && permissions.length > 0) {
    for (let i = 0; i < permissions.length; i++) {
      if (settingPermissions.includes(+permissions[i]["id"])) {
        displaySettingPermissions.push(permissions[i]);
      }
      if (companyPermissions.includes(+permissions[i]["id"])) {
        displayCompanyPermissions.push(permissions[i]);
      }
      if (jobPermissions.includes(+permissions[i]["id"])) {
        displayJobPermissions.push(permissions[i]);
      }
      if (candidatePermissions.includes(+permissions[i]["id"])) {
        displayCandidatePermissions.push(permissions[i]);
      }
      if (othersPermissions.includes(+permissions[i]["id"])) {
        displayOthersPermissions.push(permissions[i]);
      }
    }
  }

  return (
    <>
      <Container fluid={true}>
        <div className={"w-100 text-right mt-2 mb-2"}>
          <Button
            type={"button"}
            color={"primary"}
            onClick={() => history.push("/jobportal-settings/6")}
          >
            {t("buttons.back")}
          </Button>
        </div>
        <Card>
          <CardHeader>
            <h1>{t("settings.roles.rolePermission")}</h1>
          </CardHeader>
          <CardBody>
            <div>
              <h4>{roleName}</h4>
            </div>
            <div>
              <div className={"mt-5"}>
                <Formik
                  enableReinitialize
                  initialValues={formValues}
                  onSubmit={submitHandler}
                >
                  <Form>
                    <Field type={"hidden"} name={"role_id"} value={1} />
                    <FormGroup>
                      <div className={"row"}>
                        <div className="col-sm-2">
                          <div className={"permissions-columns-head"}>
                            <h5>Sätted</h5>
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
                                      value={permissions.id.toString()}
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
                            <h5>Ettevõtted</h5>
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
                                      value={permissions.id.toString()}
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
                            <h5>Tööpakkumised</h5>
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
                                      value={permissions.id.toString()}
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
                              {displayCandidatePermissions.map(
                                (permissions) => {
                                  return (
                                    <li
                                      key={permissions.id}
                                      className={"list-group-item"}
                                    >
                                      <Field
                                        type="checkbox"
                                        name="permissions"
                                        value={permissions.id.toString()}
                                      />{" "}
                                      {permissions.name}
                                    </li>
                                  );
                                }
                              )}
                            </ul>
                          </div>
                        </div>
                        <div className="col-sm-2">
                          <div className={"permissions-columns-head"}>
                            <h5>Teised</h5>
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
                                      value={permissions.id.toString()}
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
                      <div className={"w-100 text-right"}>
                        <Button
                          type={"submit"}
                          color={"primary"}
                          disabled={permissionSelector.isLoading}
                        >
                          {permissionSelector.isLoading ? (
                            <Loader />
                          ) : (
                            <>{t("buttons.save")}</>
                          )}
                        </Button>
                      </div>
                    </FormGroup>
                  </Form>
                </Formik>
              </div>
            </div>
          </CardBody>
          <Toast />
        </Card>
      </Container>
    </>
  );
};

export default RolePermissions;
