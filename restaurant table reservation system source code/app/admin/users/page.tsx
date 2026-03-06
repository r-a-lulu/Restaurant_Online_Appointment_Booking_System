"use client"

import { useState } from "react"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Badge } from "@/components/ui/badge"
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog"
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table"
import { 
  Search, 
  Eye, 
  Mail, 
  Phone, 
  Calendar, 
  Star,
  Users,
} from "lucide-react"

// Mock data
const users = [
  {
    id: 1,
    name: "Sarah Johnson",
    email: "sarah@example.com",
    phone: "(555) 111-2222",
    joinDate: "Jan 15, 2025",
    totalReservations: 12,
    lastVisit: "Feb 28, 2026",
    status: "vip",
    preferences: "Window table, vegetarian options",
  },
  {
    id: 2,
    name: "Michael Brown",
    email: "michael@example.com",
    phone: "(555) 333-4444",
    joinDate: "Mar 22, 2025",
    totalReservations: 8,
    lastVisit: "Mar 1, 2026",
    status: "regular",
    preferences: "Bar seating preferred",
  },
  {
    id: 3,
    name: "Emily Davis",
    email: "emily@example.com",
    phone: "(555) 555-6666",
    joinDate: "Jun 10, 2025",
    totalReservations: 5,
    lastVisit: "Feb 20, 2026",
    status: "regular",
    preferences: "Patio when available, nut allergy",
  },
  {
    id: 4,
    name: "James Wilson",
    email: "james@example.com",
    phone: "(555) 777-8888",
    joinDate: "Sep 5, 2024",
    totalReservations: 25,
    lastVisit: "Mar 2, 2026",
    status: "vip",
    preferences: "Private dining for business, gluten-free",
  },
  {
    id: 5,
    name: "Lisa Anderson",
    email: "lisa@example.com",
    phone: "(555) 999-0000",
    joinDate: "Nov 12, 2025",
    totalReservations: 3,
    lastVisit: "Jan 15, 2026",
    status: "new",
    preferences: "",
  },
  {
    id: 6,
    name: "Robert Taylor",
    email: "robert@example.com",
    phone: "(555) 222-3333",
    joinDate: "Aug 1, 2024",
    totalReservations: 18,
    lastVisit: "Feb 25, 2026",
    status: "vip",
    preferences: "Chef's table, wine pairings",
  },
]

type User = typeof users[0]

export default function AdminUsersPage() {
  const [searchQuery, setSearchQuery] = useState("")
  const [selectedUser, setSelectedUser] = useState<User | null>(null)
  const [detailsDialogOpen, setDetailsDialogOpen] = useState(false)

  const filteredUsers = users.filter(
    (user) =>
      user.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
      user.email.toLowerCase().includes(searchQuery.toLowerCase())
  )

  const handleViewUser = (user: User) => {
    setSelectedUser(user)
    setDetailsDialogOpen(true)
  }

  const getStatusBadge = (status: string) => {
    switch (status) {
      case "vip":
        return <Badge className="bg-yellow-100 text-yellow-800 hover:bg-yellow-100">VIP</Badge>
      case "regular":
        return <Badge variant="secondary">Regular</Badge>
      case "new":
        return <Badge className="bg-blue-100 text-blue-800 hover:bg-blue-100">New</Badge>
      default:
        return null
    }
  }

  return (
    <div className="p-6 lg:p-8 space-y-8">
      {/* Header */}
      <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 className="font-serif text-2xl md:text-3xl text-foreground">User Management</h1>
          <p className="text-muted-foreground mt-1">
            View and manage guest profiles
          </p>
        </div>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <Card>
          <CardContent className="p-6">
            <div className="flex items-center gap-4">
              <div className="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center">
                <Users className="h-5 w-5 text-primary" />
              </div>
              <div>
                <p className="text-2xl font-semibold text-foreground">{users.length}</p>
                <p className="text-sm text-muted-foreground">Total Users</p>
              </div>
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-6">
            <div className="flex items-center gap-4">
              <div className="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center">
                <Star className="h-5 w-5 text-yellow-600" />
              </div>
              <div>
                <p className="text-2xl font-semibold text-foreground">
                  {users.filter(u => u.status === "vip").length}
                </p>
                <p className="text-sm text-muted-foreground">VIP Guests</p>
              </div>
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-6">
            <div className="flex items-center gap-4">
              <div className="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                <Calendar className="h-5 w-5 text-blue-600" />
              </div>
              <div>
                <p className="text-2xl font-semibold text-foreground">
                  {users.reduce((sum, u) => sum + u.totalReservations, 0)}
                </p>
                <p className="text-sm text-muted-foreground">Total Reservations</p>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Search */}
      <Card>
        <CardContent className="p-4">
          <div className="relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input
              placeholder="Search by name or email..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="pl-9 bg-background"
            />
          </div>
        </CardContent>
      </Card>

      {/* Users Table */}
      <Card>
        <CardHeader>
          <CardTitle className="font-serif text-lg">Guest Directory</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="overflow-x-auto">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Name</TableHead>
                  <TableHead className="hidden md:table-cell">Email</TableHead>
                  <TableHead className="hidden lg:table-cell">Phone</TableHead>
                  <TableHead>Reservations</TableHead>
                  <TableHead className="hidden sm:table-cell">Status</TableHead>
                  <TableHead className="text-right">Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {filteredUsers.map((user) => (
                  <TableRow key={user.id}>
                    <TableCell className="font-medium">{user.name}</TableCell>
                    <TableCell className="hidden md:table-cell">{user.email}</TableCell>
                    <TableCell className="hidden lg:table-cell">{user.phone}</TableCell>
                    <TableCell>{user.totalReservations}</TableCell>
                    <TableCell className="hidden sm:table-cell">
                      {getStatusBadge(user.status)}
                    </TableCell>
                    <TableCell className="text-right">
                      <Button
                        size="sm"
                        variant="ghost"
                        onClick={() => handleViewUser(user)}
                      >
                        <Eye className="h-4 w-4" />
                      </Button>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </div>

          {filteredUsers.length === 0 && (
            <div className="text-center py-12">
              <p className="text-muted-foreground">No users found matching your search.</p>
            </div>
          )}
        </CardContent>
      </Card>

      {/* User Details Dialog */}
      <Dialog open={detailsDialogOpen} onOpenChange={setDetailsDialogOpen}>
        <DialogContent className="max-w-md">
          <DialogHeader>
            <DialogTitle>Guest Profile</DialogTitle>
          </DialogHeader>
          {selectedUser && (
            <div className="space-y-6">
              <div className="flex items-center gap-4">
                <div className="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center text-primary text-xl font-semibold">
                  {selectedUser.name.split(" ").map(n => n[0]).join("")}
                </div>
                <div>
                  <h3 className="font-medium text-foreground text-lg">{selectedUser.name}</h3>
                  {getStatusBadge(selectedUser.status)}
                </div>
              </div>

              <div className="space-y-3">
                <div className="flex items-center gap-3 text-sm">
                  <Mail className="h-4 w-4 text-primary" />
                  <span>{selectedUser.email}</span>
                </div>
                <div className="flex items-center gap-3 text-sm">
                  <Phone className="h-4 w-4 text-primary" />
                  <span>{selectedUser.phone}</span>
                </div>
                <div className="flex items-center gap-3 text-sm">
                  <Calendar className="h-4 w-4 text-primary" />
                  <span>Member since {selectedUser.joinDate}</span>
                </div>
              </div>

              <div className="grid grid-cols-2 gap-4 p-4 bg-muted rounded-sm">
                <div className="text-center">
                  <p className="text-2xl font-semibold text-foreground">{selectedUser.totalReservations}</p>
                  <p className="text-xs text-muted-foreground">Total Visits</p>
                </div>
                <div className="text-center">
                  <p className="text-sm font-medium text-foreground">{selectedUser.lastVisit}</p>
                  <p className="text-xs text-muted-foreground">Last Visit</p>
                </div>
              </div>

              {selectedUser.preferences && (
                <div className="p-4 bg-accent/50 rounded-sm">
                  <p className="text-sm font-medium text-foreground mb-1">Preferences & Notes</p>
                  <p className="text-sm text-muted-foreground">{selectedUser.preferences}</p>
                </div>
              )}

              <div className="flex gap-2">
                <Button variant="outline" className="flex-1">
                  <Mail className="h-4 w-4 mr-2" />
                  Email
                </Button>
                <Button className="flex-1 bg-primary text-primary-foreground">
                  View History
                </Button>
              </div>
            </div>
          )}
        </DialogContent>
      </Dialog>
    </div>
  )
}
