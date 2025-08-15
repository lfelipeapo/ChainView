import { useState } from 'react'
import { Table, Button, Drawer, Form, Input, Space } from 'antd'
import { PlusOutlined, EditOutlined, DeleteOutlined, NodeIndexOutlined } from '@ant-design/icons'
import { useNavigate } from 'react-router-dom'
import type { AreaNode } from '../hooks/useAreaTree'

interface Props {
  data: AreaNode[]
}

interface AreaFormValues {
  name: string
}

export default function AreaTable({ data }: Props) {
  const [open, setOpen] = useState(false)
  const [editing, setEditing] = useState<AreaNode | null>(null)
  const [form] = Form.useForm<AreaFormValues>()
  const navigate = useNavigate()

  const handleAdd = () => {
    setEditing(null)
    form.resetFields()
    setOpen(true)
  }

  const onEdit = (record: AreaNode) => {
    setEditing(record)
    form.setFieldsValue(record)
    setOpen(true)
  }

  const onClose = () => setOpen(false)

  const onFinish = (values: AreaFormValues) => {
    // TODO: integrate with API
    console.log('save', values)
    onClose()
  }

  const columns = [
    { title: 'Name', dataIndex: 'name', key: 'name' },
    {
      title: 'Actions',
      key: 'actions',
      render: (_: unknown, record: AreaNode) => (
        <Space>
          <Button icon={<EditOutlined />} onClick={() => onEdit(record)} />
          <Button icon={<DeleteOutlined />} danger />
          <Button icon={<NodeIndexOutlined />} onClick={() => navigate(`/flow/area/${record.id}`)}>
            Fluxo
          </Button>
        </Space>
      ),
    },
  ]

  return (
    <>
      <Button type="primary" icon={<PlusOutlined />} onClick={handleAdd} style={{ marginBottom: 16 }}>
        Add Area
      </Button>
      <Table rowKey="id" columns={columns} dataSource={data} pagination={false} />
      <Drawer title={editing ? 'Edit Area' : 'New Area'} open={open} onClose={onClose} destroyOnClose>
        <Form layout="vertical" form={form} onFinish={onFinish}>
          <Form.Item name="name" label="Name" rules={[{ required: true }]}> 
            <Input />
          </Form.Item>
          <Button type="primary" htmlType="submit">
            Save
          </Button>
        </Form>
      </Drawer>
    </>
  )
}
