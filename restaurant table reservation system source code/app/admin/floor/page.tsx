"use client"

import { useState } from "react"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Badge } from "@/components/ui/badge"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog"
import { Label } from "@/components/ui/label"
import { Input } from "@/components/ui/input"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { cn } from "@/lib/utils"
import { Plus, Edit2, Trash2, Users } from "lucide-react"

// Mock data
const zones = [
  {
    id: "dining-room",
    name: "Main Dining Room",
    tables: [
      { id: 1, name: "Table 1", seats: 2, status: "available" },
      { id: 2, name: "Table 2", seats: 2, status: "occupied", reservation: "Sarah J." },
      { id: 3, name: "Table 3", seats: 4, status: "reserved", reservation: "7:00 PM" },
      { id: 4, name: "Table 4", seats: 4, status: "available" },
      { id: 5, name: "Table 5", seats: 6, status: "occupied", reservation: "Wilson party" },
      { id: 6, name: "Table 6", seats: 4, status: "available" },
      { id: 7, name: "Table 7", seats: 2, status: "reserved", reservation: "7:30 PM" },
      { id: 8, name: "Table 8", seats: 8, status: "available" },
      { id: 9, name: "Table 9", seats: 4, status: "occupied", reservation: "Brown family" },
      { id: 10, name: "Table 10", seats: 2, status: "available" },
    ],
  },
  {
    id: "patio",
    name: "The Patio",
    tables: [
      { id: 11, name: "Garden 1", seats: 2, status: "available" },
      { id: 12, name: "Garden 2", seats: 4, status: "occupied", reservation: "Davis party" },
      { id: 13, name: "Fountain", seats: 4, status: "reserved", reservation: "8:00 PM" },
      { id: 14, name: "Pergola", seats: 6, status: "available" },
      { id: 15, name: "Corner", seats: 4, status: "occupied", reservation: "Anderson" },
    ],
  },
  {
    id: "bar",
    name: "The Bar",
    tables: [
      { id: 16, name: "Bar 1", seats: 2, status: "occupied", reservation: "Walk-in" },
      { id: 17, name: "Bar 2", seats: 2, status: "available" },
      { id: 18, name: "High Top 1", seats: 4, status: "reserved", reservation: "6:30 PM" },
      { id: 19, name: "High Top 2", seats: 4, status: "available" },
      { id: 20, name: "Lounge", seats: 6, status: "occupied", reservation: "VIP" },
    ],
  },
]

type Table = typeof zones[0]["tables"][0]

export default function FloorManagementPage() {
  const [selectedZone, setSelectedZone] = useState("dining-room")
  const [editDialogOpen, setEditDialogOpen] = useState(false)
  const [addDialogOpen, setAddDialogOpen] = useState(false)
  const [selectedTable, setSelectedTable] = useState<Table | null>(null)

  const currentZone = zones.find(z => z.id === selectedZone)

  const getStatusColor = (status: string) => {
    switch (status) {
      case "available":
        return "bg-green-100 border-green-300 text-green-800"
      case "occupied":
        return "bg-red-100 border-red-300 text-red-800"
      case "reserved":
        return "bg-yellow-100 border-yellow-300 text-yellow-800"
      default:
        return "bg-muted border-border"
    }
  }

  const handleEditTable = (table: Table) => {
    setSelectedTable(table)
    setEditDialogOpen(true)
  }

  return (
    <div className="p-6 lg:p-8 space-y-8">
      {/* Header */}
      <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 className="font-serif text-2xl md:text-3xl text-foreground">Floor Management</h1>
          <p className="text-muted-foreground mt-1">
            Manage tables and dining zones
          </p>
        </div>
        <Button 
          onClick={() => setAddDialogOpen(true)}
          className="bg-primary text-primary-foreground hover:bg-primary/90"
        >
          <Plus className="h-4 w-4 mr-2" />
          Add Table
        </Button>
      </div>

      {/* Zone Tabs */}
      <Tabs value={selectedZone} onValueChange={setSelectedZone}>
        <TabsList className="grid w-full max-w-lg grid-cols-3">
          {zones.map((zone) => (
            <TabsTrigger key={zone.id} value={zone.id}>
              {zone.name.replace("Main ", "").replace("The ", "")}
            </TabsTrigger>
          ))}
        </TabsList>

        {zones.map((zone) => (
          <TabsContent key={zone.id} value={zone.id} className="space-y-6">
            {/* Zone Stats */}
            <div className="grid grid-cols-3 gap-4">
              <Card>
                <CardContent className="p-4 text-center">
                  <p className="text-2xl font-semibold text-green-600">
                    {zone.tables.filter(t => t.status === "available").length}
                  </p>
                  <p className="text-sm text-muted-foreground">Available</p>
                </CardContent>
              </Card>
              <Card>
                <CardContent className="p-4 text-center">
                  <p className="text-2xl font-semibold text-yellow-600">
                    {zone.tables.filter(t => t.status === "reserved").length}
                  </p>
                  <p className="text-sm text-muted-foreground">Reserved</p>
                </CardContent>
              </Card>
              <Card>
                <CardContent className="p-4 text-center">
                  <p className="text-2xl font-semibold text-red-600">
                    {zone.tables.filter(t => t.status === "occupied").length}
                  </p>
                  <p className="text-sm text-muted-foreground">Occupied</p>
                </CardContent>
              </Card>
            </div>

            {/* Tables Grid */}
            <Card>
              <CardHeader>
                <CardTitle className="font-serif text-lg">{zone.name} Tables</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                  {zone.tables.map((table) => (
                    <div
                      key={table.id}
                      className={cn(
                        "relative p-4 rounded-sm border-2 cursor-pointer transition-all hover:shadow-md",
                        getStatusColor(table.status)
                      )}
                      onClick={() => handleEditTable(table)}
                    >
                      <div className="flex items-center justify-between mb-2">
                        <span className="font-medium text-sm">{table.name}</span>
                        <Edit2 className="h-3 w-3 opacity-50" />
                      </div>
                      <div className="flex items-center gap-1 text-xs opacity-75">
                        <Users className="h-3 w-3" />
                        <span>{table.seats} seats</span>
                      </div>
                      {table.reservation && (
                        <p className="text-xs mt-2 truncate">{table.reservation}</p>
                      )}
                    </div>
                  ))}
                </div>

                {/* Legend */}
                <div className="flex flex-wrap gap-4 mt-6 pt-6 border-t">
                  <div className="flex items-center gap-2">
                    <div className="w-4 h-4 rounded bg-green-100 border border-green-300" />
                    <span className="text-sm text-muted-foreground">Available</span>
                  </div>
                  <div className="flex items-center gap-2">
                    <div className="w-4 h-4 rounded bg-yellow-100 border border-yellow-300" />
                    <span className="text-sm text-muted-foreground">Reserved</span>
                  </div>
                  <div className="flex items-center gap-2">
                    <div className="w-4 h-4 rounded bg-red-100 border border-red-300" />
                    <span className="text-sm text-muted-foreground">Occupied</span>
                  </div>
                </div>
              </CardContent>
            </Card>
          </TabsContent>
        ))}
      </Tabs>

      {/* Edit Table Dialog */}
      <Dialog open={editDialogOpen} onOpenChange={setEditDialogOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Edit Table</DialogTitle>
            <DialogDescription>
              Update table details and status
            </DialogDescription>
          </DialogHeader>
          {selectedTable && (
            <div className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="tableName">Table Name</Label>
                <Input id="tableName" defaultValue={selectedTable.name} className="bg-background" />
              </div>
              <div className="space-y-2">
                <Label htmlFor="seats">Seats</Label>
                <Select defaultValue={selectedTable.seats.toString()}>
                  <SelectTrigger className="bg-background">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    {[2, 4, 6, 8, 10, 12].map((n) => (
                      <SelectItem key={n} value={n.toString()}>{n} seats</SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
              <div className="space-y-2">
                <Label htmlFor="status">Status</Label>
                <Select defaultValue={selectedTable.status}>
                  <SelectTrigger className="bg-background">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="available">Available</SelectItem>
                    <SelectItem value="reserved">Reserved</SelectItem>
                    <SelectItem value="occupied">Occupied</SelectItem>
                  </SelectContent>
                </Select>
              </div>
            </div>
          )}
          <DialogFooter className="flex-col sm:flex-row gap-2">
            <Button variant="outline" className="text-destructive hover:text-destructive">
              <Trash2 className="h-4 w-4 mr-2" />
              Delete Table
            </Button>
            <div className="flex-1" />
            <Button variant="outline" onClick={() => setEditDialogOpen(false)}>
              Cancel
            </Button>
            <Button className="bg-primary text-primary-foreground">
              Save Changes
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {/* Add Table Dialog */}
      <Dialog open={addDialogOpen} onOpenChange={setAddDialogOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Add New Table</DialogTitle>
            <DialogDescription>
              Add a new table to the selected zone
            </DialogDescription>
          </DialogHeader>
          <div className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="zone">Zone</Label>
              <Select defaultValue={selectedZone}>
                <SelectTrigger className="bg-background">
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  {zones.map((zone) => (
                    <SelectItem key={zone.id} value={zone.id}>{zone.name}</SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
            <div className="space-y-2">
              <Label htmlFor="newTableName">Table Name</Label>
              <Input id="newTableName" placeholder="e.g., Table 11" className="bg-background" />
            </div>
            <div className="space-y-2">
              <Label htmlFor="newSeats">Seats</Label>
              <Select defaultValue="4">
                <SelectTrigger className="bg-background">
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  {[2, 4, 6, 8, 10, 12].map((n) => (
                    <SelectItem key={n} value={n.toString()}>{n} seats</SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
          </div>
          <DialogFooter>
            <Button variant="outline" onClick={() => setAddDialogOpen(false)}>
              Cancel
            </Button>
            <Button className="bg-primary text-primary-foreground">
              Add Table
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  )
}
