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
  Toast,
} from "reactstrap";
import { Field, Form, Formik } from "formik";
import { DataLoader, Loader } from "../../constants/loaders";
import { getAllUsers } from "../../../redux/actions/usersAction";
import { useDispatch, useSelector } from "react-redux";
import {
  createTask,
  getAllAssignedTasks,
  getMentions,
  markTaskComplete,
} from "../../../redux/actions/taskManagementActions";
import Multiselect from "multiselect-react-dropdown";
import { EditorState, convertToRaw } from "draft-js";
import Editor from "@draft-js-plugins/editor";
import createMentionPlugin, {
  defaultSuggestionsFilter,
} from "@draft-js-plugins/mention";
import editorStyles from "./editorStyles.module.css";
import "@draft-js-plugins/mention/lib/plugin.css";
import asterik from "../../../assets/images/asterisk.png";
import { disablePastDate } from "../../constants/disabledPreviousDates";
import Paginator from "react-hooks-paginator";
import { NoListFound } from "../../constants/noRecordFound";
import {
  FormValidationErrors,
  SubmitFormValidationErrors,
} from "../../constants/errors";
import { useTranslation } from "react-i18next";
import { TaskValidationSchema } from "../Dashboard/validations/formValidations";
import { validateHtmlTags } from "../Dashboard/validations/validateFuncations";

const Taskboard = ({ fixNavbar, userRole }) => {
  const dispatch = useDispatch();

  const { t } = useTranslation();

  const initialValues = {
    user_pr_ids: [],
    description: "",
    deadline: "",
  };

  const [formValues, setFormValues] = useState(initialValues);
  const [modal, setModal] = useState(false);
  const [allUsers, setAllUsers] = useState([]);
  const [allTasksList, getAllTasksList] = useState([]);
  const [checkedComplete, setCheckedComplete] = useState(false);
  const [editorState, setEditorState] = useState(EditorState.createEmpty());
  const [open, setOpen] = useState(false);
  const [suggestions, setSuggestions] = useState([]);
  const [totalRecords, setTotalRecords] = useState(0);
  const [pageNumber, setPageNumber] = useState(1);
  const [offset, setOffset] = useState(0);
  const [errors, setErrors] = useState({});
  const [formErrors, setFormErrors] = useState({});

  const ref = useRef(null);

  const { MentionSuggestions, plugins } = useMemo(() => {
    const mentionPlugin = createMentionPlugin();
    const { MentionSuggestions } = mentionPlugin;
    const plugins = [mentionPlugin];
    return { plugins, MentionSuggestions };
  }, []);

  const content = editorState.getCurrentContent();
  const editorDescription = convertToRaw(content);

  const toggleModal = () => {
    setModal(!modal);
  };

  const fetchAllAssignedTasks = async () => {
    try {
      const response = await dispatch(getAllAssignedTasks(pageNumber));
      let responseData = response.payload.data;
      let totalRecords = response.payload.total;
      getAllTasksList(responseData);
      setTotalRecords(totalRecords);
    } catch (error) {
      throw error;
    }
  };

  const fetchAllUsers = async () => {
    try {
      const response = await dispatch(getAllUsers());
      if (response.payload.data) {
        setAllUsers(response.payload.data.Users.data);
      }
    } catch (error) {
      throw error;
    }
  };

  useEffect(() => {
    fetchAllAssignedTasks();
    fetchAllUsers();
  }, [pageNumber]);

  const taskSelector = useSelector((state) => state.tasks);
  const getAllAssignedTasksSelector = useSelector(
    (state) => state.tasks.all_assigned_task
  );
  const mentionSelector = useSelector((state) => state.tasks.mentions.result);
  const settingsSelector = useSelector((state) => state.settings);

  useEffect(() => {
    if (mentionSelector) {
      setSuggestions(mentionSelector);
    }
  }, [mentionSelector]);

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

  const handleComplete = async (id) => {
    const confirm = window.confirm(
      "Are You Sure, You Want To Complete This Task?"
    );
    if (confirm === true) {
      await dispatch(markTaskComplete(id, setCheckedComplete, pageNumber));
    } else if (confirm === false) {
      setCheckedComplete(false);
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
                        {t("todo.toWhome")} {/* Kellele{" "} */}
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
                        {t("todo.description")} {/* Kirjeldus{" "} */}
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
                          id={"mention-editor"}
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
                        {t("todo.deadline")} {/* Tähtaeg{" "} */}
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
                          <>{t("todo.createTaskButton")} </>
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
    <>
      {renderModal()}
      <div className={`section-body ${fixNavbar ? "marginTop" : ""} p-0 mt-3`}>
        <Row>
          <Col sm={12} md={12} lg={12}>
            <Card>
              <CardBody>
                <div className="table-responsive todo_list">
                  <Table className="table table-hover table-striped table-vcenter mb-0">
                    <thead>
                      <tr>
                        <th>
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
                            {t("todo.addNewTodo")}
                            {/* Lisa uus */}
                          </Button>
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
                          {t("todo.deadline")}
                          {/* Tähtaeg */}
                        </th>
                        {/*<th className="w100">Priority</th>*/}
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
                      </tr>
                    </thead>
                    <tbody>
                      {allTasksList ? (
                        allTasksList !== undefined &&
                        allTasksList.length > 0 ? (
                          allTasksList.map((items, index) => (
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
                                {items.hr_task_status_id === 1 ? (
                                  <label className="custom-control custom-checkbox">
                                    <input
                                      type="checkbox"
                                      className="custom-control-input"
                                      name="complete-task"
                                      onClick={(e) => handleComplete(items.id)}
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
                                    <span className="custom-control-label">
                                      {items.description}
                                    </span>
                                  </label>
                                ) : (
                                  items.hr_task_status_id === 2 && (
                                    <label className="custom-control custom-checkbox">
                                      <input
                                        type="checkbox"
                                        className="custom-control-input"
                                        name="complete-task"
                                        defaultValue="option1"
                                        checked
                                        readOnly
                                      />
                                      <span className="custom-control-label">
                                        {items.description}
                                      </span>
                                    </label>
                                  )
                                )}
                              </td>
                              <td
                                className="text-right"
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
                                {items.deadline}
                              </td>
                              {/*<td>*/}
                              {/*  <span className="tag tag-danger ml-0 mr-0">*/}
                              {/*    HIGH*/}
                              {/*  </span>*/}
                              {/*</td>*/}
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
                            </tr>
                          ))
                        ) : (
                          <tr>
                            <td>
                              <NoListFound />
                            </td>
                          </tr>
                        )
                      ) : typeof getAllAssignedTasksSelector === "object" ? (
                        <tr>
                          <td>
                            <Row>
                              <Col sm={12} md={12} className={"p-4"}>
                                {getAllAssignedTasksSelector.message}
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
      </div>
    </>
  );
};

export default Taskboard;
