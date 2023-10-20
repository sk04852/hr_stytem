import React, { useState } from "react";
import AvatarEditor from "react-avatar-editor";
import { Button, Modal, ModalBody, ModalFooter, ModalHeader } from "reactstrap";
import { useTranslation } from "react-i18next";

const ReactAvatarEditorModal = ({
  image,
  editorRef,
  isCropAvatarModalOpen,
  setIsCropAvatarModalOpen,
  scale,
  setFile,
}) => {
  const { t } = useTranslation();

  const handleSave = () => {
    const canvas = editorRef.current.getImageScaledToCanvas();
    canvas.toBlob((blob) => {
      setFile(blob);
      setIsCropAvatarModalOpen(false);
    });
  };

  return (
    <Modal
      isOpen={isCropAvatarModalOpen}
      toggle={() => setIsCropAvatarModalOpen(!isCropAvatarModalOpen)}
    >
      <ModalHeader>
        {t("candidates.bookmarks.cropImageModalHeading")}
      </ModalHeader>
      <ModalBody>
        {image && (
          <div className="text-center">
            <AvatarEditor
              ref={editorRef}
              image={image}
              width={300}
              height={250}
              border={50}
              borderRadius={125}
              scale={scale}
              rotate={0}
            />
          </div>
        )}
      </ModalBody>
      <ModalFooter>
        <Button type="submit" color="primary" onClick={handleSave}>
          {t("buttons.save")}
        </Button>
        <Button
          type="button"
          color="danger"
          onClick={() => setIsCropAvatarModalOpen(!isCropAvatarModalOpen)}
        >
          {t("buttons.cancel")}
        </Button>
      </ModalFooter>
    </Modal>
  );
};

export default ReactAvatarEditorModal;
