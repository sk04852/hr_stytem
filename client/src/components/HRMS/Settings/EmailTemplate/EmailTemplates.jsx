import React, { useEffect, useState } from "react";
import "../../Candidates/candidate.css";
import {
  Button,
  Card,
  CardBody,
  CardHeader,
  CardTitle,
  Dropdown,
  DropdownItem,
  DropdownMenu,
  Input,
  Table,
} from "reactstrap";
import { useHistory } from "react-router-dom";
import { useDispatch, useSelector } from "react-redux";
import {
  deleteEmailTemplates,
  getEmailTemplates,
} from "../../../../redux/actions/emailTemplateActions";
import { DataLoader } from "../../../constants/loaders";
import Toast from "../../../constants/toast";
import { NoListFound } from "../../../constants/noRecordFound";
import Paginator from "react-hooks-paginator";
import { useTranslation } from "react-i18next";

const EmailTemplates = (props) => {
  const { userPermission } = props;
  const history = useHistory();
  const dispatch = useDispatch();

  const { t } = useTranslation();

  const [searchTerm, setSearchTerm] = useState("");
  const [data, setData] = useState([]);
  // PAGINATION STATES START
  const [offset, setOffset] = useState(0);
  const [pageNumber, setPageNumber] = useState(1);
  const [totalRecords, setTotalRecords] = useState(0);
  // PAGINATION STATES END

  useEffect(() => {
    dispatch(getEmailTemplates(pageNumber));
  }, [pageNumber]);

  const emailTemplateSelector = useSelector(
    (state) => state.emailTemplates.data
  );
  const emailTemplateLoading = useSelector(
    (state) => state.emailTemplates.isLoading
  );
  const settingsSelector = useSelector((state) => state.settings);

  useEffect(() => {
    if (emailTemplateSelector) {
      setData(emailTemplateSelector.data);
      if (emailTemplateSelector.total) {
        setTotalRecords(emailTemplateSelector.total);
      }
    }
  }, [emailTemplateSelector]);

  const handleDelete = (e, id) => {
    const confirm = window.confirm("Are You Sure?");
    if (confirm === true) {
      dispatch(deleteEmailTemplates([id]));
    }
  };

  return (
    <>
      <Card>
        <CardHeader>
          <CardTitle className={"w-100 d-flex flex-row"}>
            <h3>{t("settings.emailTemplates.heading")}</h3>
          </CardTitle>
          {userPermission.includes("Can-Add-Mail-Templates") ? (
            <div className={"w-100 text-right"}>
              <Button
                type={"button"}
                color={"primary"}
                onClick={() =>
                  history.push("/jobportal-settings/3/add-template")
                }
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
                <i className="fe fe-plus mr-2" />
                {t("settings.emailTemplates.addNewTemplate")}
              </Button>
            </div>
          ) : null}
        </CardHeader>
        <CardBody>
          <div className="card-options w-100 d-flex justify-content-end">
            <form>
              <div className="input-group">
                <Input
                  type="text"
                  className="form-control form-control-sm"
                  placeholder="Search Template"
                  name="s"
                  onChange={(e) => setSearchTerm(e.target.value)}
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
              </div>
            </form>
          </div>
          <Table>
            <thead>
              <tr>
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
                  #
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
                  {t("settings.emailTemplates.templateName")}
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
                  {t("settings.emailTemplates.templateKey")}
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
                  {t("actions")}
                </th>
              </tr>
            </thead>
            <tbody>
              {!emailTemplateLoading && data !== undefined ? (
                data.length > 0 ? (
                  data
                    .filter((e) => {
                      if (searchTerm === "") {
                        return e;
                      } else if (
                        e.title.toLowerCase().includes(searchTerm.toLowerCase())
                      ) {
                        return e;
                      } else if (
                        e.template_key
                          .toLowerCase()
                          .includes(searchTerm.toLowerCase())
                      ) {
                        return e;
                      }
                    })
                    .map((items, index) => (
                      <tr key={index}>
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
                          {(pageNumber - 1) * 10 + (index + 1)}
                        </th>
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
                          {items.title}
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
                          {items.template_key}
                        </td>
                        <td>
                          <Dropdown>
                            <i
                              className="fa fa-angle-down dropdown-toggle"
                              id="dropdownMenuButton"
                              data-toggle="dropdown"
                              aria-haspopup="true"
                              aria-expanded="false"
                              style={{
                                fontSize: `${
                                  settingsSelector.FontSize === "Large"
                                    ? "large"
                                    : settingsSelector.FontSize ===
                                      "Extra Large"
                                    ? "x-large"
                                    : "inherit"
                                }`,
                              }}
                            />
                            <DropdownMenu aria-labelledby="dropdownMenuButton">
                              <div
                                className={
                                  "w-100 d-flex justify-content-center"
                                }
                              >
                                <DropdownItem
                                  className={"hover-overlay"}
                                  onClick={() =>
                                    history.push(
                                      `/jobportal-settings/view-template/${items.id}`
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
                                  <i className="fa fa-eye text-dark" />
                                </DropdownItem>
                                {userPermission.includes(
                                  "Can-Edit-Mail-Template"
                                ) ? (
                                  <DropdownItem
                                    onClick={() =>
                                      history.push({
                                        pathname: `/jobportal-settings/edit-template/${items.id}`,
                                      })
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
                                    <i className="fa fa-edit text-dark" />
                                  </DropdownItem>
                                ) : null}
                                {userPermission.includes(
                                  "Can-Delete-Mail-Templates"
                                ) ? (
                                  <DropdownItem
                                    onClick={(e) => handleDelete(e, items.id)}
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
                                    <i className="fa fa-trash-o text-dark" />
                                  </DropdownItem>
                                ) : null}
                              </div>
                            </DropdownMenu>
                          </Dropdown>
                        </td>
                      </tr>
                    ))
                ) : (
                  <tr>
                    <td>
                      <NoListFound />
                    </td>
                  </tr>
                )
              ) : (
                <tr>
                  <td>
                    <DataLoader />
                  </td>
                </tr>
              )}
            </tbody>
          </Table>
        </CardBody>
        <Paginator
          totalRecords={totalRecords}
          pageLimit={10}
          pageNeighbours={2}
          setOffset={setOffset}
          currentPage={pageNumber}
          setCurrentPage={setPageNumber}
        />
      </Card>
      <Toast />
    </>
  );
};

export default EmailTemplates;
