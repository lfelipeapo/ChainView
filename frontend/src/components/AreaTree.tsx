import { Button, Modal, Form, Input, Tree } from 'antd'
import { PlusOutlined } from '@ant-design/icons'
import { useState } from 'react'
import { useAreaTree } from '../hooks/useAreaTree'
import type { AreaNode } from '../hooks/useAreaTree'

export default function AreaTree() {
  const areaQuery = useAreaTree()
  const [open, setOpen] = useState(false)
  const [parentId, setParentId] = useState<number | null>(null)
  const [form] = Form.useForm()

  const onAdd = (id: number | null) => {
    setParentId(id)
    form.resetFields()
    setOpen(true)
  }

  interface AreaFormValues {
    name: string
  }

  const onFinish = (values: AreaFormValues) => {
    // TODO: integrate with API
    console.log('create', { ...values, parentId })
    setOpen(false)
  }

  return (
    <>
      <Button type="primary" icon={<PlusOutlined />} onClick={() => onAdd(null)} style={{ marginBottom: 16 }}>
        Add Root Area
      </Button>
      <Tree
        treeData={(areaQuery.data ?? []) as any}
        fieldNames={{ title: 'name', key: 'id', children: 'children' }}
        titleRender={(nodeData) => {
          const node = nodeData as AreaNode
          return (
            <span>
              {node.name}
              <Button type="link" size="small" icon={<PlusOutlined />} onClick={() => onAdd(node.id)} />
            </span>
          )
        }}
      />
      <Modal open={open} onCancel={() => setOpen(false)} onOk={form.submit} title="New Area">
        <Form form={form} layout="vertical" onFinish={onFinish}>
          <Form.Item name="name" label="Name" rules={[{ required: true }]}> 
            <Input />
          </Form.Item>
        </Form>
      </Modal>
    </>
  )
}
