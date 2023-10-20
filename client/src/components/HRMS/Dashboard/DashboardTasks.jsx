import React, {
  useCallback,
  useEffect,
  useMemo,
  useRef,
  useState,
} from "react";
import {
  Button,
  Card,
  CardBody,
  Col,
  FormGroup,
  Label,
  Modal,
  ModalBody,
  ModalHeader,
  Row,
  Table,
} from "reactstrap";
import { DataLoader, Loader } from "../../constants/loaders";
import {
  createTask,
  deleteTask,
  getMentions,
} from "../../../redux/actions/taskManagementActions";
import { useDispatch, useSelector } from "react-redux";
import Paginator from "react-hooks-paginator";
import { Field, Form, Formik, ErrorMessage } from "formik";
import Multiselect from "multiselect-react-dropdown";
import Editor from "@draft-js-plugins/editor";
import createMentionPlugin, {
  defaultSuggestionsFilter,
} from "@draft-js-plugins/mention";
import { convertToRaw, EditorState } from "draft-js";
import editorStyles from "../TaskManagement/editorStyles.module.css";
import "@draft-js-plugins/mention/lib/plugin.css";
import {
  getAllUsers,
  getUserProfile,
} from "../../../redux/actions/usersAction";
import asterik from "../../../assets/images/asterisk.png";
import { disablePastDate } from "../../constants/disabledPreviousDates";
import Toast from "../../constants/toast";
import {
  FormValidationErrors,
  SubmitFormValidationErrors,
} from "../../constants/errors";
import { useTranslation } from "react-i18next";
import { TaskValidationSchema } from "./validations/formValidations";
import { validateHtmlTags } from "./validations/validateFuncations";

const DashboardTasks = ({ pageNumber, setPageNumber }) => {
  const dispatch = useDispatch();

  const { t } = useTranslation();

  const initialValues = {
    user_pr_ids: [],
    description: "",
    deadline: "",
  };

  const [formValues, setFormValues] = useState(initialValues);
  const [allTasksList, setAllTasksList] = useState([]);
  const [totalRecords, setTotalRecords] = useState(0);
  const [offset, setOffset] = useState(0);
  const [modal, setModal] = useState(false);
  const [open, setOpen] = useState(false);
  const [suggestions, setSuggestions] = useState([]);
  const [editorState, setEditorState] = useState(EditorState.createEmpty());
  const [allUsers, setAllUsers] = useState([]);
  const [errors, setErrors] = useState({});
  const [userPermission, setPermissions] = useState();
  const [formErrors, setFormErrors] = useState({});

  const toggleModal = () => {
    setModal(!modal);
  };

  const ref = useRef(null);

  const { MentionSuggestions, plugins } = useMemo(() => {
    const mentionPlugin = createMentionPlugin();
    const { MentionSuggestions } = mentionPlugin;
    const plugins = [mentionPlugin];
    return { plugins, MentionSuggestions };
  }, []);

  const content = editorState.getCurrentContent();
  const editorDescription = convertToRaw(content);

  useEffect(() => {
    const fetchPermissions = async () => {
      try {
        const response = await dispatch(getUserProfile());
        setPermissions(response.payload.data.permissions);
      } catch (error) {
        throw error;
      }
    };
    fetchPermissions();
  }, []);

  useEffect(() => {
    const fetchAllUsers = async () => {
      try {
        const response = await dispatch(getAllUsers());
        setAllUsers(response.payload.data.Users.data);
      } catch (error) {
        throw error;
      }
    };
    fetchAllUsers();
  }, []);

  const getAllTasksSelector = useSelector(
    (state) => state.tasks.all_created_tasks
  );
  const taskSelector = useSelector((state) => state.tasks);
  const mentionSelector = useSelector((state) => state.tasks.mentions.result);
  const settingsSelector = useSelector((state) => state.settings);

  useEffect(() => {
    if (getAllTasksSelector) {
      setAllTasksList(getAllTasksSelector.data);
      if (getAllTasksSelector.total) {
        setTotalRecords(getAllTasksSelector.total);
      }
    }

    if (mentionSelector) {
      setSuggestions(mentionSelector);
    }
  }, [getAllTasksSelector, mentionSelector]);

  const handleDelete = (id) => {
    const confirm = window.confirm(
      "Oled kindel, et soovid kustutada selle ülesande?"
    );
    if (confirm === true) {
      dispatch(deleteTask(id, pageNumber));
    }
  };

  const onSelectSkills = (selectedList, selectedItem) => {
    let ids = [];
    selectedList.forEach((items) => {
      ids.push(items.userpr_id);
    });

    setFormValues({ ...formValues, user_pr_ids: ids });
  };

  const onRemoveSkills = (selectedList, selectedItem) => {
    setFormValues({ ...formValues, user_pr_ids: selectedList });
  };

  const handleAddNewTask = (values) => {
    values["description"] = editorDescription.blocks[0].text;

    // validate
    let error = {};
    error = validateHtmlTags(values, setFormErrors);

    // submit form if there is no error
    if (Object.keys(error).length === 0) {
      dispatch(
        createTask(values, setModal, setErrors, setEditorState, EditorState)
      );
    }
  };

  const onOpenChange = useCallback((_open) => {
    setOpen(_open);
  }, []);

  const debounce = (func) => {
    let timer;
    return function (...args) {
      const context = this;
      if (timer) clearTimeout(timer);
      timer = setTimeout(() => {
        timer = null;
        func.apply(context, args);
      }, 500);
    };
  };

  const onSearchChange = ({ value }) => {
    if (value !== "" && value.length > 0) {
      dispatch(getMentions(value));
      setSuggestions(defaultSuggestionsFilter(value, suggestions));
    }
  };

  const optimizedSearchFn = useCallback(debounce(onSearchChange), []);

  const renderModal = () => {
    return (
      <Modal isOpen={modal} toggle={toggleModal}>
        <ModalHeader
          toggle={toggleModal}
          style={{
            fontSize: `${
              settingsSelector.FontSize === "Large"
                ? "large"
                : settingsSelector.FontSize === "Extra Large"
                ? "x-large"
                : "14px"
            }`,
          }}
          className={"mb-2"}
        >
          {t("todo.addNewTodoModalHeading")}
          {/* Lisa ülesanne */}
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
          <Row>
            <Formik
              initialValues={formValues}
              enableReinitialize
              validationSchema={TaskValidationSchema}
              onSubmit={(values) => handleAddNewTask(values)}
            >
              {({ errors, touched }) => (
                <Form
                  className={"w-100"}
                  id={"add-new-task-form"}
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
                  <fieldset disabled={taskSelector.isLoading}>
                    <FormGroup>
                      <Label
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
                        {t("taskboard.toWhome")} {/* Kellele{" "} */}
                        <img
                          src={asterik}
                          height={10}
                          width={10}
                          className="mt-n2"
                        />
                      </Label>
                      <Multiselect
                        options={allUsers} // Options to display in the dropdown
                        selectedValues={"" || []} // Preselected value to persist in dropdown
                        onSelect={onSelectSkills} // Function will trigger on select event
                        onRemove={onRemoveSkills} // Function will trigger on remove event
                        displayValue="name" // Property name to display in the dropdown options
                      />
                    </FormGroup>
                    <FormGroup>
                      <Label
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
                        {t("taskboard.description")} {/* Kirjeldus{" "} */}
                        <img
                          src={asterik}
                          height={10}
                          width={10}
                          className="mt-n2"
                        />
                      </Label>
                      <div className={editorStyles.editor}>
                        <Editor
                          editorKey={"editor"}
                          editorState={editorState}
                          onChange={setEditorState}
                          plugins={plugins}
                          ref={ref}
                        />
                        <MentionSuggestions
                          open={open}
                          onOpenChange={onOpenChange}
                          suggestions={suggestions}
                          onSearchChange={optimizedSearchFn}
                        />
                      </div>
                    </FormGroup>
                    <FormGroup>
                      <Label
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
                        {t("taskboard.deadline")} {/* Tähtaeg{" "} */}
                        <img
                          src={asterik}
                          height={10}
                          width={10}
                          className="mt-n2"
                        />
                      </Label>
                      <Field
                        type="date"
                        className="form-control"
                        placeholder="Tähtaeg"
                        id={"deadline"}
                        name={"deadline"}
                        min={disablePastDate()}
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
                      <ErrorMessage name="deadline">
                        {(msg) => <span style={{ color: "red" }}>{msg}</span>}
                      </ErrorMessage>
                    </FormGroup>
                    <div
                      className={"w-100 d-flex flex-row justify-content-end"}
                    >
                      <Button
                        type={"submit"}
                        color="primary"
                        className={"mr-3"}
                        disabled={taskSelector.isLoading}
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
                        {taskSelector.isLoading ? (
                          <Loader />
                        ) : (
                          <>{t("taskboard.createTaskButton")}</>
                        )}
                      </Button>
                      <Button
                        color="secondary"
                        onClick={toggleModal}
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
                        {/* Tühista */}
                      </Button>
                    </div>
                  </fieldset>
                </Form>
              )}
            </Formik>
          </Row>
        </ModalBody>
      </Modal>
    );
  };

  return (
    <Row>
      {renderModal()}
      <Col>
        <Card>
          <CardBody>
            <div className="table-responsive todo_list">
              <Table className="table table-hover table-striped table-vcenter mb-0">
                <thead>
                  <tr>
                    <th>
                      {userPermission &&
                      userPermission.includes("Can-Assign-Tasks") ? (
                        <Button
                          type={"button"}
                          color={"info"}
                          className="btn btn-sm"
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
                          {t("taskboard.addNewTasks")}
                          {/* Lisa uus */}
                        </Button>
                      ) : null}
                    </th>
                    <th
                      className="w150 text-right"
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
                      {t("taskboard.deadline")}
                      {/* Tähtaeg */}
                    </th>
                    <th
                      className="w80"
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
                      <i className="icon-user" />
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
                      {t("delete")}
                      {/* Kustuta */}
                    </th>
                  </tr>
                </thead>
                <tbody>
                  {allTasksList ? (
                    allTasksList &&
                    allTasksList.length > 0 &&
                    allTasksList !== undefined ? (
                      allTasksList.map((items, index) => (
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
                            {items.hr_task_status_id === 1 ? (
                              <span>{items.description}</span>
                            ) : (
                              <span>
                                <del>{items.description}</del>
                              </span>
                            )}
                          </td>
                          <td
                            className="text-right"
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
                            {items.deadline}
                          </td>
                          <td>
                            <span
                              className="avatar avatar-pink"
                              data-toggle="tooltip"
                              data-placement="top"
                              data-original-title="Avatar Name"
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
                              NG
                            </span>
                          </td>
                          <td>
                            <button
                              type="button"
                              className="btn btn-icon btn-sm js-sweetalert"
                              title="Delete"
                              data-type="confirm"
                              onClick={() => handleDelete(items.id)}
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
                          <DataLoader />
                        </td>
                      </tr>
                    )
                  ) : typeof getAllTasksSelector === "object" ? (
                    <tr>
                      <td>
                        <Row>
                          <Col sm={12} md={12} className={"p-4"}>
                            {getAllTasksSelector.message}
                          </Col>
                        </Row>
                      </td>
                    </tr>
                  ) : (
                    <tr>
                      <td>
                        <DataLoader />
                      </td>
                    </tr>
                  )}
                </tbody>
              </Table>
            </div>
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
      </Col>
      <Toast />
    </Row>
  );
};

export default DashboardTasks;
